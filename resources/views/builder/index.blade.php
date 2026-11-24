@extends('layouts.app', ['title' => 'Create Your Website in Minutes - AI Builds My Website'])

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Hero Banner -->
    <div class="text-center max-w-3xl mx-auto mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold mb-4">
            <i class="fa-solid fa-bolt"></i> Instant Generation &bull; Zero Hallucination Guarantee
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
            Share what you have.<br>
            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">AI builds your ready-to-publish website.</span>
        </h1>
        <p class="text-lg text-slate-600">
            Provide any mix of business name, city, social links, Google listing, or upload price lists & brochures. We crawl, extract real facts, and build your bespoke website.
        </p>

        <!-- Industry Starter Templates -->
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sample Templates:</span>
            <button type="button" onclick="loadPreset('bakery')" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-white border border-slate-200 text-slate-700 hover:border-blue-500 hover:text-blue-600 shadow-sm transition">
                <i class="fa-solid fa-bread-slice text-amber-500 mr-1"></i> Artisan Bakery & Cafe
            </button>
            <button type="button" onclick="loadPreset('plumbing')" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-white border border-slate-200 text-slate-700 hover:border-blue-500 hover:text-blue-600 shadow-sm transition">
                <i class="fa-solid fa-wrench text-sky-500 mr-1"></i> HVAC & Home Services
            </button>
        </div>
    </div>

    <!-- Intake Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-200 p-6 sm:p-10 mb-12">
        <form action="{{ route('builder.store') }}" method="POST" enctype="multipart/form-data" id="intakeForm">
            @csrf

            <!-- Section 1: Business Identity -->
            <div class="mb-8">
                <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-bold">1</span>
                    Business Identity
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Business Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="bizName" required placeholder="e.g. Sharma & Sons Artisan Bakery" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">City / Location</label>
                        <input type="text" name="city" id="bizCity" placeholder="e.g. Bandra West, Mumbai" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Category / Industry</label>
                        <input type="text" name="category" id="bizCategory" placeholder="e.g. Bakery & Pastry Shop" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" id="bizPhone" placeholder="e.g. +91 98201 23456" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="bizEmail" placeholder="e.g. hello@sharmabakery.com" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                </div>
            </div>

            <!-- Section 2: Links & Social Profiles -->
            <div class="mb-8 pt-6 border-t border-slate-100">
                <h2 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">2</span>
                    Online Listings & Social Profiles (Any mix)
                </h2>
                <p class="text-xs text-slate-500 mb-4">Our crawler extracts real photos, reviews, and hours. If social networks block scraping, we handle it gracefully without crashing.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            <i class="fa-brands fa-google text-red-500 mr-1"></i> Google Business Listing / Maps Link
                        </label>
                        <input type="url" name="google_business_url" id="bizGoogle" placeholder="https://maps.google.com/?cid=..." class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            <i class="fa-brands fa-instagram text-pink-600 mr-1"></i> Instagram Profile
                        </label>
                        <input type="url" name="social_instagram" id="bizInstagram" placeholder="https://instagram.com/sharma_bakery" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            <i class="fa-brands fa-facebook text-blue-600 mr-1"></i> Facebook Page
                        </label>
                        <input type="url" name="social_facebook" id="bizFacebook" placeholder="https://facebook.com/sharmasbakery" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            <i class="fa-brands fa-linkedin text-sky-700 mr-1"></i> LinkedIn Company Page
                        </label>
                        <input type="url" name="social_linkedin" id="bizLinkedin" placeholder="https://linkedin.com/company/sharma-sons" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                </div>
            </div>

            <!-- Section 3: Files & Catalogs -->
            <div class="mb-8 pt-6 border-t border-slate-100">
                <h2 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">3</span>
                    Upload Files: PDF Brochures, Price Lists, or Photos
                </h2>
                <p class="text-xs text-slate-500 mb-4">We extract services, menus, and prices directly. Files are safely screened against prompt injection.</p>

                <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-6 text-center cursor-pointer transition bg-slate-50/50 hover:bg-blue-50/20" onclick="document.getElementById('fileUploadInput').click()">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-2"></i>
                    <p class="text-sm font-medium text-slate-700">Click to upload or drag files here</p>
                    <p class="text-xs text-slate-500 mt-1">Supports PDF (menu/brochure), CSV/TXT (price list), and PNG/JPG/WebP photos (Max 20MB)</p>
                    <input type="file" name="files[]" id="fileUploadInput" multiple class="hidden" onchange="showSelectedFiles(this)">
                </div>
                <div id="fileListPreview" class="mt-3 flex flex-wrap gap-2 text-xs font-medium text-slate-600"></div>
            </div>

            <!-- Edge Cases Protection Banner -->
            <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-4 mb-8">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-shield-halved text-amber-600 text-lg mt-0.5"></i>
                    <div class="text-xs text-amber-900">
                        <span class="font-bold">Built-In Edge Case Protections Active:</span>
                        <ul class="list-disc list-inside mt-1 space-y-0.5 text-amber-800">
                            <li><strong>Golden Rule:</strong> Never hallucinates facts. Missing data will prompt you in the editor.</li>
                            <li><strong>Source Conflict Detector:</strong> Automatically flags differing phone numbers or addresses across sources.</li>
                            <li><strong>Indirect Prompt Injection Defense:</strong> Untrusted text in PDFs is strictly sandboxed.</li>
                            <li><strong>Resilient AI Fallback:</strong> If Gemini is unreachable or rate-limited, high-quality template fallback takes over.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-4">
                <button type="submit" id="submitBtn" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Build My Website Now
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Sites -->
    @if(isset($recentSites) && $recentSites->isNotEmpty())
        <div class="mt-12">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Recently Created Websites</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($recentSites as $s)
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-slate-900">{{ $s->title }}</h4>
                                <p class="text-xs text-slate-500">{{ $s->business->city ?? 'Location not specified' }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $s->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $s->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                            <a href="{{ route('builder.editor', $s->id) }}" class="text-blue-600 hover:underline font-medium">Open Editor &rarr;</a>
                            @if($s->is_published)
                                <a href="{{ $s->public_url }}" target="_blank" class="text-slate-500 hover:text-slate-800">Visit Public Site <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i></a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function loadPreset(type) {
        if (type === 'bakery') {
            document.getElementById('bizName').value = "Sharma & Sons Artisan Bakery";
            document.getElementById('bizCity').value = "Bandra West, Mumbai";
            document.getElementById('bizCategory').value = "Artisan Bakery & Cafe";
            document.getElementById('bizPhone').value = "+91 98201 54321";
            document.getElementById('bizEmail').value = "orders@sharmabakerymumbai.com";
            document.getElementById('bizGoogle').value = "https://maps.google.com/?cid=123456789";
            document.getElementById('bizInstagram').value = "https://instagram.com/sharma_artisan_bakery";
            document.getElementById('bizFacebook').value = "https://facebook.com/sharmaartisanbakery";
        } else if (type === 'plumbing') {
            document.getElementById('bizName').value = "Apex Plumbing & HVAC Solutions";
            document.getElementById('bizCity').value = "Indiranagar, Bangalore";
            document.getElementById('bizCategory').value = "Plumbing, Electrical & AC Repair";
            document.getElementById('bizPhone').value = "+91 80 4123 9876";
            document.getElementById('bizEmail').value = "service@apexplumbhvac.in";
            document.getElementById('bizGoogle').value = "https://maps.google.com/?cid=987654321";
            document.getElementById('bizInstagram').value = "https://instagram.com/apex_solutions_blr";
            document.getElementById('bizLinkedin').value = "https://linkedin.com/company/apex-plumbing-hvac";
        }
    }

    function showSelectedFiles(input) {
        const preview = document.getElementById('fileListPreview');
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const badge = document.createElement('span');
                badge.className = 'px-2.5 py-1 rounded-md bg-slate-200 text-slate-800 flex items-center gap-1.5';
                badge.innerHTML = `<i class="fa-regular fa-file"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                preview.appendChild(badge);
            });
        }
    }

    document.getElementById('intakeForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Reading files & generating website...`;
    });
</script>
@endsection
