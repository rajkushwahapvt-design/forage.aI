@extends('layouts.app', ['title' => 'Visual Editor - ' . $site->title])

@section('content')
<div class="h-[calc(100vh-4rem)] flex flex-col overflow-hidden bg-slate-100">
    <!-- Top Action Bar -->
    <div class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between shrink-0 shadow-sm z-30">
        <div class="flex items-center gap-4">
            <a href="{{ route('builder.index') }}" class="text-slate-500 hover:text-slate-800 text-sm font-medium flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
            <div class="h-5 w-px bg-slate-200"></div>
            <div>
                <h1 class="font-bold text-slate-900 text-base leading-tight">{{ $site->title }}</h1>
                <p class="text-xs text-slate-500 flex items-center gap-2">
                    <span>Slug: <code class="bg-slate-100 px-1 py-0.5 rounded text-blue-600">/site/{{ $site->slug }}</code></span>
                    &bull;
                    <span id="publishStatusBadge" class="px-2 py-0.2 rounded text-[11px] font-semibold {{ $site->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $site->is_published ? 'Published' : 'Draft (Unpublished)' }}
                    </span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Styling Switcher Form -->
            <form action="{{ route('builder.updateStyle', $site->id) }}" method="POST" class="hidden sm:flex items-center gap-2">
                @csrf
                <select name="theme" class="text-xs rounded-lg border-slate-300 py-1.5 px-2.5 bg-slate-50 border outline-none font-medium" onchange="this.form.submit()">
                    <option value="modern" {{ $site->theme === 'modern' ? 'selected' : '' }}>Modern Clean</option>
                    <option value="bold" {{ $site->theme === 'bold' ? 'selected' : '' }}>Bold Contractor</option>
                    <option value="warm" {{ $site->theme === 'warm' ? 'selected' : '' }}>Warm Earthy (Bakery/Cafe)</option>
                    <option value="elegant" {{ $site->theme === 'elegant' ? 'selected' : '' }}>Elegant Luxury</option>
                    <option value="tech" {{ $site->theme === 'tech' ? 'selected' : '' }}>Tech Dark</option>
                </select>
                <input type="hidden" name="primary_color" value="{{ $site->primary_color }}">
            </form>

            <!-- Public View Button -->
            <a href="{{ $site->public_url }}" target="_blank" id="previewSiteBtn" class="px-3.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Preview Site
            </a>

            <!-- Publish / Unpublish Toggle -->
            <button type="button" onclick="togglePublish()" id="publishToggleBtn" class="px-4 py-1.5 rounded-lg font-bold text-xs shadow-sm transition flex items-center gap-2 {{ $site->is_published ? 'bg-rose-500 hover:bg-rose-600 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                <i class="fa-solid {{ $site->is_published ? 'fa-eye-slash' : 'fa-globe' }}"></i>
                <span id="publishBtnText">{{ $site->is_published ? 'Unpublish' : 'Publish to Public URL' }}</span>
            </button>
        </div>
    </div>

    <!-- Conflict Resolution Alert Bar (If Disagreeing Sources Exist) -->
    @if($site->business->pendingConflicts->isNotEmpty())
        <div class="bg-rose-600 text-white px-6 py-2.5 text-xs flex items-center justify-between shadow-inner shrink-0">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <span class="font-semibold">Conflicting Facts Detected Between Sources:</span>
                <span>Differing data was found across your Google listing, social links, or documents. Please choose the correct value:</span>
            </div>
            <div class="flex items-center gap-2">
                @foreach($site->business->pendingConflicts as $conflict)
                    <form action="{{ route('builder.resolveConflict', $site->business->id) }}" method="POST" class="inline-flex items-center gap-1">
                        @csrf
                        <input type="hidden" name="conflict_id" value="{{ $conflict->id }}">
                        <span class="font-bold underline uppercase">{{ $conflict->field }}:</span>
                        <button type="submit" name="chosen_value" value="{{ $conflict->value_a }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold px-2 py-0.5 rounded text-[11px] transition">
                            Keep "{{ $conflict->value_a }}" ({{ $conflict->source_a }})
                        </button>
                        <button type="submit" name="chosen_value" value="{{ $conflict->value_b }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold px-2 py-0.5 rounded text-[11px] transition">
                            Keep "{{ $conflict->value_b }}" ({{ $conflict->source_b }})
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Split-Pane Workspace -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Left Sidebar: Section Manager & AI Controls -->
        <div class="w-80 sm:w-96 bg-white border-r border-slate-200 flex flex-col shrink-0 overflow-y-auto">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <span class="font-bold text-xs uppercase tracking-wider text-slate-500">Website Sections ({{ $site->sections->count() }})</span>
                <span class="text-xs text-slate-400">Click to edit or regenerate</span>
            </div>

            <div class="p-3 space-y-2">
                @foreach($site->sections as $section)
                    <div class="rounded-xl border border-slate-200 p-3 hover:border-blue-400 hover:shadow-sm transition bg-white group">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-sm text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-slate-400 group-hover:text-blue-600"></i>
                                {{ $section->title ?? ucfirst(str_replace('_', ' ', $section->type)) }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $section->generation_source === 'fallback_template' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ $section->generation_source }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mb-3 line-clamp-1">Type: {{ $section->type }}</p>

                        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                            <!-- Edit Modal Trigger -->
                            <button type="button" onclick="openEditModal({{ $section->id }}, '{{ addslashes($section->title) }}', {{ json_encode($section->content) }})" class="flex-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition text-center">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                            </button>

                            <!-- AI Regenerate Section Trigger -->
                            <button type="button" onclick="openRegenerateModal({{ $section->id }}, '{{ $section->type }}')" class="flex-1 px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition text-center">
                                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Regenerate
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Main View: Live Responsive Website Preview -->
        <div class="flex-1 flex flex-col bg-slate-200 p-4 overflow-hidden">
            <div class="bg-white rounded-t-xl border-t border-x border-slate-300 px-4 py-2 flex items-center justify-between text-xs text-slate-500 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-400 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span>
                    <span class="ml-2 font-mono text-slate-600 bg-slate-100 px-3 py-1 rounded-md border border-slate-200">
                        {{ url('/site/' . $site->slug) }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setPreviewWidth('100%')" class="px-2 py-1 rounded hover:bg-slate-100 text-slate-700" title="Desktop View">
                        <i class="fa-solid fa-desktop"></i>
                    </button>
                    <button type="button" onclick="setPreviewWidth('768px')" class="px-2 py-1 rounded hover:bg-slate-100 text-slate-700" title="Tablet View">
                        <i class="fa-solid fa-tablet-screen-button"></i>
                    </button>
                    <button type="button" onclick="setPreviewWidth('390px')" class="px-2 py-1 rounded hover:bg-slate-100 text-slate-700" title="Mobile View">
                        <i class="fa-solid fa-mobile-screen"></i>
                    </button>
                </div>
            </div>
            <!-- Embedded Preview Iframe -->
            <div class="flex-1 bg-white rounded-b-xl border border-slate-300 shadow-md flex justify-center overflow-hidden">
                <iframe id="previewIframe" src="{{ $site->public_url }}?preview=1" class="w-full h-full border-0 transition-all duration-300"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: Edit Section Content -->
<div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="font-bold text-slate-900 text-base" id="editModalTitle">Edit Section</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
        </div>
        <div class="flex-1 overflow-y-auto mb-4">
            <form id="editSectionForm">
                <input type="hidden" id="editSectionId">
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Section Title</label>
                    <input type="text" id="editSectionTitleInput" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Section Content (JSON)</label>
                    <textarea id="editSectionContentJson" rows="12" class="w-full font-mono text-xs rounded-xl border border-slate-300 p-3 outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50"></textarea>
                </div>
            </form>
        </div>
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</button>
            <button type="button" onclick="saveSectionChanges()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20">Save Changes</button>
        </div>
    </div>
</div>

<!-- Modal 2: AI Regenerate Section -->
<div id="regenerateModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles text-indigo-600"></i> Regenerate Section
            </h3>
            <button type="button" onclick="closeRegenerateModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
        </div>
        <input type="hidden" id="regenSectionId">
        <p class="text-xs text-slate-600 mb-3">Provide instructions for how the AI should rewrite or restyle this section:</p>
        <textarea id="regenPromptInput" rows="4" placeholder="e.g. Make it sound more luxurious, highlight our 15 years experience, and add a guarantee badge." class="w-full text-xs rounded-xl border border-slate-300 p-3 outline-none focus:ring-2 focus:ring-indigo-500 mb-4"></textarea>

        <div class="flex items-center justify-end gap-3">
            <button type="button" onclick="closeRegenerateModal()" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">Cancel</button>
            <button type="button" id="confirmRegenBtn" onclick="executeRegeneration()" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-500/20">
                <i class="fa-solid fa-arrows-rotate mr-1"></i> Regenerate with AI
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const iframe = document.getElementById('previewIframe');

    function refreshPreview() {
        iframe.src = iframe.src;
    }

    function setPreviewWidth(width) {
        iframe.style.width = width;
    }

    // Publish / Unpublish Toggle
    function togglePublish() {
        const btn = document.getElementById('publishToggleBtn');
        const badge = document.getElementById('publishStatusBadge');
        const text = document.getElementById('publishBtnText');

        fetch("{{ route('api.site.publish', $site->id) }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.is_published) {
                badge.className = 'px-2 py-0.2 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800';
                badge.textContent = 'Published';
                btn.className = 'px-4 py-1.5 rounded-lg font-bold text-xs shadow-sm transition flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white';
                text.textContent = 'Unpublish';
            } else {
                badge.className = 'px-2 py-0.2 rounded text-[11px] font-semibold bg-amber-100 text-amber-800';
                badge.textContent = 'Draft (Unpublished)';
                btn.className = 'px-4 py-1.5 rounded-lg font-bold text-xs shadow-sm transition flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white';
                text.textContent = 'Publish to Public URL';
            }
            refreshPreview();
        });
    }

    // Edit Modal Logic
    function openEditModal(sectionId, title, content) {
        document.getElementById('editSectionId').value = sectionId;
        document.getElementById('editSectionTitleInput').value = title;
        document.getElementById('editSectionContentJson').value = JSON.stringify(content, null, 2);
        document.getElementById('editModalTitle').textContent = `Edit Section: ${title}`;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function saveSectionChanges() {
        const id = document.getElementById('editSectionId').value;
        const title = document.getElementById('editSectionTitleInput').value;
        let content;
        try {
            content = JSON.parse(document.getElementById('editSectionContentJson').value);
        } catch (e) {
            alert('Invalid JSON in content area: ' + e.message);
            return;
        }

        fetch(`/api/section/${id}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ title, content })
        })
        .then(res => res.json())
        .then(data => {
            closeEditModal();
            refreshPreview();
        });
    }

    // Regenerate Modal Logic
    function openRegenerateModal(sectionId, type) {
        document.getElementById('regenSectionId').value = sectionId;
        document.getElementById('regenPromptInput').value = `Enhance the ${type} section with clear value propositions, modern phrasing, and high-converting copy.`;
        document.getElementById('regenerateModal').classList.remove('hidden');
    }

    function closeRegenerateModal() {
        document.getElementById('regenerateModal').classList.add('hidden');
    }

    function executeRegeneration() {
        const id = document.getElementById('regenSectionId').value;
        const prompt = document.getElementById('regenPromptInput').value;
        const btn = document.getElementById('confirmRegenBtn');

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Generating...`;

        fetch(`/api/section/${id}/regenerate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ prompt })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-arrows-rotate mr-1"></i> Regenerate with AI`;
            closeRegenerateModal();
            refreshPreview();
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-arrows-rotate mr-1"></i> Regenerate with AI`;
            alert('Regeneration failed: ' + err.message);
        });
    }
</script>
@endsection
