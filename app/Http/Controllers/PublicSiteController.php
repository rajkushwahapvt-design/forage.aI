<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Site;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    /**
     * Render the published public website.
     */
    public function show(string $slug)
    {
        $site = Site::where('slug', $slug)
            ->with(['business', 'visibleSections'])
            ->firstOrFail();

        // If not published, allow preview only if preview flag is present
        if (!$site->is_published && !request()->has('preview')) {
            return response()->view('public.unpublished', ['site' => $site], 403);
        }

        return view('public.site', compact('site'));
    }

    /**
     * Handle user inquiry / contact form submission from public site.
     */
    public function submitInquiry(Request $request, string $slug)
    {
        $site = Site::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'service_requested' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Inquiry::create([
            'site_id' => $site->id,
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'service_requested' => $validated['service_requested'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return back()->with('inquiry_success', 'Thank you! Your message has been received. The business owner will get back to you shortly.');
    }
}
