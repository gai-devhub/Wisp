@extends('admin.base-admin')

@section('admin-section', 'ads')

@section('content')
<div class="content-section active" id="ads">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-800 m-0">
            <span class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="fas fa-bullhorn"></i>
            </span>
            Advertisement Management
        </h2>
        <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl cursor-pointer border-0 transition-colors shadow-sm shadow-indigo-200" onclick="openAddAdModal()">
            <i class="fas fa-plus"></i> Create New Ad
        </button>
    </div>

    <div class="db-card overflow-hidden">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ad Info</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ads as $ad)
                    <tr>
                        <td>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center text-slate-400">
                                    @if($ad->image_path)
                                        <img src="{{ asset('storage/' . $ad->image_path) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-image"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 text-sm">{{ $ad->title }}</div>
                                    @if($ad->link_url)
                                        <a href="{{ $ad->link_url }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors no-underline block mt-0.5">{{ Str::limit($ad->link_url, 30) }}</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 capitalize">
                                {{ $ad->display_location }}
                            </span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold capitalize {{ $ad->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $ad->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="font-weight: 500;">
                            {{ $ad->created_at->format('M j, Y') }}
                        </td>
                        <td>
                            <div class="action-buttons" style="justify-content: flex-end;">
                                <button type="button" class="action-btn edit" title="Edit Ad" onclick="openEditAdModal({{ json_encode($ad) }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" onsubmit="event.preventDefault(); showAdminConfirm('Delete this ad?', () => this.submit());" class="m-0 p-0" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Delete Ad">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h4 class="text-base font-bold text-slate-700 m-0 mb-1">No Advertisements Found</h4>
                            <p class="text-sm font-medium text-slate-500 m-0">Create your first ad to display to users.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($ads, 'currentPage') && $ads->lastPage() > 0)
            <div class="custom-pagination">
                <div class="pagination-info">
                    Showing page {{ $ads->currentPage() }} of {{ $ads->lastPage() }}
                </div>
                <div class="pagination-btns">
                    <button type="button" class="pagination-btn" aria-label="Previous" @if($ads->onFirstPage()) disabled @endif onclick="@if(!$ads->onFirstPage()) location.href='{{ $ads->previousPageUrl() }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                    <button type="button" class="pagination-btn" aria-label="Next" @if(!$ads->hasMorePages()) disabled @endif onclick="@if($ads->hasMorePages()) location.href='{{ $ads->nextPageUrl() }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="adModal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="db-card w-full max-w-lg shadow-2xl flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between p-5 border-b border-slate-200">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-800 m-0">Create New Ad</h3>
            <button type="button" class="border-0 bg-transparent text-2xl cursor-pointer text-slate-400 hover:text-red-500 leading-none" onclick="closeAdModal()">&times;</button>
        </div>
        <form id="adForm" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden">
            @csrf
            <div id="methodField"></div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Ad Title</label>
                    <input type="text" name="title" id="adTitle" required placeholder="e.g., Summer Special Sale"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Description (Optional)</label>
                    <textarea name="content" id="adContent" rows="2" placeholder="Short banner message..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all resize-y"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Details (Optional)</label>
                    <textarea name="details" id="adDetails" rows="4" placeholder="Detailed ad message for the modal..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all resize-y"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Image</label>
                    <div style="position: relative; overflow: hidden; display: inline-block; width: 100%;">
                        <div style="padding: 12px 16px; background: #f8fafc; color: #4f46e5; border-radius: 12px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; cursor: pointer; border: 2px dashed #cbd5e1; transition: all 0.2s;" onmouseover="this.style.borderColor='#4f46e5'; this.style.background='#e0e7ff';" onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';">
                            <i class="fas fa-cloud-upload-alt"></i> <span id="fileName">Choose Image File...</span>
                        </div>
                        <input type="file" name="image" accept="image/*" style="position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;" onchange="document.getElementById('fileName').innerText = this.files[0] ? this.files[0].name : 'Choose Image File...'">
                    </div>
                    <p class="text-xs font-medium text-slate-400 mt-2">Recommended size: 1200x400 for banners.</p>
                    <div id="currentImage" class="mt-3 hidden">
                        <img src="" alt="" class="h-16 rounded-lg border border-slate-200 object-cover">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Target URL</label>
                    <input type="url" name="link_url" id="adLink" placeholder="https://example.com/promo"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Display Location</label>
                        <select name="display_location" id="adLocation" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                            <option value="dashboard">User Dashboard</option>
                            <option value="templates">Template Viewer</option>
                            <option value="all">All Locations</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Status</label>
                        <select name="status" id="adStatus" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="p-5 border-t border-slate-200 flex items-center justify-end gap-3 bg-slate-50">
                <button type="button" class="px-5 py-2.5 rounded-xl font-semibold text-sm bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors cursor-pointer" onclick="closeAdModal()">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-semibold text-sm bg-indigo-600 text-white hover:bg-indigo-700 transition-colors border-0 cursor-pointer">Save Advertisement</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('adModal');
    const form = document.getElementById('adForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');

    function openAddAdModal() {
        modalTitle.textContent = 'Create New Ad';
        form.action = "{{ route('admin.ads.store') }}";
        methodField.innerHTML = '';
        form.reset();
        document.getElementById('currentImage').style.display = 'none';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function openEditAdModal(ad) {
        modalTitle.textContent = 'Edit Advertisement';
        form.action = `/admin/ads/${ad.id}`;
        methodField.innerHTML = '@method("PUT")';
        
        document.getElementById('adTitle').value = ad.title;
        document.getElementById('adContent').value = ad.content || '';
        document.getElementById('adDetails').value = ad.details || '';
        document.getElementById('adLink').value = ad.link_url || '';
        document.getElementById('adLocation').value = ad.display_location;
        document.getElementById('adStatus').value = ad.is_active ? 'active' : 'inactive';
        
        if (ad.image_path) {
            const imgDiv = document.getElementById('currentImage');
            imgDiv.style.display = 'block';
            imgDiv.classList.remove('hidden');
            imgDiv.querySelector('img').src = `/storage/${ad.image_path}`;
        } else {
            document.getElementById('currentImage').style.display = 'none';
            document.getElementById('currentImage').classList.add('hidden');
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAdModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
