<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\DataConflict;
use App\Models\MediaFile;
use App\Models\Site;
use App\Models\SiteSection;
use App\Services\AI\WebsiteGeneratorService;
use App\Services\Extractor\ConflictDetectorService;
use App\Services\Extractor\DisambiguationService;
use App\Services\Extractor\FileParserService;
use App\Services\Extractor\WebCrawlerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteBuilderController extends Controller
{
    protected WebCrawlerService $crawler;
    protected FileParserService $fileParser;
    protected DisambiguationService $disambiguator;
    protected ConflictDetectorService $conflictDetector;
    protected WebsiteGeneratorService $generator;

    public function __construct(
        WebCrawlerService $crawler,
        FileParserService $fileParser,
        DisambiguationService $disambiguator,
        ConflictDetectorService $conflictDetector,
        WebsiteGeneratorService $generator
    ) {
        $this->crawler = $crawler;
        $this->fileParser = $fileParser;
        $this->disambiguator = $disambiguator;
        $this->conflictDetector = $conflictDetector;
        $this->generator = $generator;
    }

    /**
     * Show the business input wizard.
     */
    public function index()
    {
        $recentSites = Site::with('business')->latest()->take(5)->get();
        return view('builder.index', compact('recentSites'));
    }

    /**
     * Handle business submission: crawl links, parse files, detect conflicts, generate website.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'google_business_url' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_facebook' => 'nullable|url',
            'social_linkedin' => 'nullable|url',
            'files.*' => 'nullable|file|max:20480', // 20MB limit
        ]);

        // 1. Create or retrieve Business
        $business = Business::create([
            'name' => $validated['name'],
            'city' => $validated['city'] ?? null,
            'category' => $validated['category'] ?? 'General Business',
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'google_maps_url' => $validated['google_business_url'] ?? null,
            'social_links' => array_filter([
                'instagram' => $validated['social_instagram'] ?? null,
                'facebook' => $validated['social_facebook'] ?? null,
                'linkedin' => $validated['social_linkedin'] ?? null,
            ]),
            'status' => 'analyzing',
        ]);

        $allExtractedFacts = [];

        // 2. Crawl Google Business URL or website if provided
        if (!empty($validated['google_business_url'])) {
            $crawlResult = $this->crawler->crawlUrl($validated['google_business_url']);
            $business->dataSources()->create([
                'source_type' => 'google_business',
                'source_url' => $validated['google_business_url'],
                'raw_content' => $crawlResult['raw_content'] ?? null,
                'parsed_facts' => $crawlResult['extracted_facts'] ?? [],
                'scrape_status' => $crawlResult['status'],
                'error_message' => $crawlResult['error'] ?? null,
            ]);

            if (!empty($crawlResult['extracted_facts'])) {
                $allExtractedFacts['google_business'] = $crawlResult['extracted_facts'];

                // Check conflict against user input phone
                if ($business->phone && !empty($crawlResult['extracted_facts']['phones'])) {
                    $this->conflictDetector->detectAndRecordConflicts(
                        $business,
                        ['phone' => $business->phone],
                        'User Intake Form',
                        ['phones' => $crawlResult['extracted_facts']['phones']],
                        'Google Listing Crawl'
                    );
                }
            }
        }

        // 3. Crawl Social Links (handles anti-scraping gracefully)
        foreach ($business->social_links as $network => $url) {
            $crawl = $this->crawler->crawlUrl($url);
            $business->dataSources()->create([
                'source_type' => 'social_link',
                'source_url' => $url,
                'raw_content' => $crawl['raw_content'] ?? null,
                'parsed_facts' => $crawl['extracted_facts'] ?? [],
                'scrape_status' => $crawl['status'],
                'error_message' => $crawl['error'] ?? null,
            ]);
        }

        // 4. Parse uploaded files (PDF, CSV, images)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $parsed = $this->fileParser->parseFile($file);
                $path = $file->store('uploads/' . $business->id, 'public');

                $mediaType = 'gallery';
                if ($parsed['type'] === 'pdf') $mediaType = 'pdf_document';
                if ($parsed['type'] === 'csv_pricelist') $mediaType = 'pricelist_csv';

                $media = MediaFile::create([
                    'business_id' => $business->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'type' => $mediaType,
                    'extracted_text' => $parsed['parsed_facts'] ?? [],
                ]);

                if ($parsed['type'] === 'pdf' || $parsed['type'] === 'csv_pricelist') {
                    $allExtractedFacts[$parsed['type']] = $parsed['parsed_facts'];

                    // Check conflict between PDF phone and existing phone
                    if ($business->phone && !empty($parsed['parsed_facts']['phones'])) {
                        $this->conflictDetector->detectAndRecordConflicts(
                            $business,
                            ['phone' => $business->phone],
                            'Intake Form',
                            ['phones' => $parsed['parsed_facts']['phones']],
                            'Uploaded ' . strtoupper($parsed['type'])
                        );
                    }
                }
            }
        }

        // 5. Generate Website (Gemini AI or graceful fallback)
        $site = $this->generator->generateWebsite($business, $allExtractedFacts);

        return redirect()->route('builder.editor', $site->id)->with('success', 'Website generated successfully!');
    }

    /**
     * View live editor for a generated site.
     */
    public function editor(Site $site)
    {
        $site->load(['business.pendingConflicts', 'sections']);
        return view('builder.editor', compact('site'));
    }

    /**
     * Resolve a data conflict (e.g., picking between Phone A and Phone B).
     */
    public function resolveConflict(Request $request, Business $business)
    {
        $validated = $request->validate([
            'conflict_id' => 'required|exists:data_conflicts,id',
            'chosen_value' => 'required|string',
        ]);

        $conflict = DataConflict::where('business_id', $business->id)->findOrFail($validated['conflict_id']);
        $conflict->update([
            'status' => 'resolved',
            'resolved_value' => $validated['chosen_value'],
        ]);

        // Update the business field with the chosen resolved value
        if ($conflict->field === 'phone') {
            $business->update(['phone' => $validated['chosen_value']]);
        } elseif ($conflict->field === 'address') {
            $business->update(['full_address' => $validated['chosen_value']]);
        }

        // Check if all conflicts are resolved
        $remaining = $business->pendingConflicts()->count();
        if ($remaining === 0) {
            $business->update(['has_unresolved_conflicts' => false]);
        }

        return back()->with('success', "Conflict resolved: {$conflict->field} set to '{$validated['chosen_value']}'.");
    }

    /**
     * Update section content directly from the editor.
     */
    public function updateSection(Request $request, SiteSection $section)
    {
        $validated = $request->validate([
            'title' => 'nullable|string',
            'content' => 'required|array',
            'is_visible' => 'nullable|boolean',
        ]);

        $section->update([
            'title' => $validated['title'] ?? $section->title,
            'content' => $validated['content'],
            'is_visible' => $request->has('is_visible') ? (bool)$validated['is_visible'] : $section->is_visible,
            'generation_source' => 'user_edited',
        ]);

        return response()->json(['success' => true, 'section' => $section]);
    }

    /**
     * Regenerate an individual section with AI or user prompt.
     */
    public function regenerateSection(Request $request, SiteSection $section)
    {
        $prompt = $request->input('prompt', 'Improve copy, make it more persuasive and modern.');
        $updatedSection = $this->generator->regenerateSection($section, $prompt);

        return response()->json([
            'success' => true,
            'section' => $updatedSection,
            'message' => 'Section regenerated successfully!'
        ]);
    }

    /**
     * Toggle public publishing status.
     */
    public function togglePublish(Request $request, Site $site)
    {
        $isPublished = !$site->is_published;
        $site->update([
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'is_published' => $isPublished,
            'public_url' => $site->public_url,
            'message' => $isPublished ? 'Website published to public URL!' : 'Website unpublished.',
        ]);
    }

    /**
     * Update site styling (theme, primary color, font).
     */
    public function updateStyle(Request $request, Site $site)
    {
        $validated = $request->validate([
            'theme' => 'required|string|in:modern,bold,elegant,warm,tech',
            'primary_color' => 'required|string',
            'font_family' => 'nullable|string',
        ]);

        $site->update($validated);

        return back()->with('success', 'Site style updated!');
    }
}
