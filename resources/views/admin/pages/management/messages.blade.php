@extends('admin.base-admin')

@section('admin-section', 'messages')

@section('content')
<div class="content-section active" id="messages">
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-weight: 700; color: var(--text-main); font-size: 1.6rem; margin: 0; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-envelope text-primary" style="opacity: 0.9;"></i>
            Message Management
        </h2>
    </div>

    <div class="db-card overflow-hidden">
        {{-- Filter bar --}}
        <div class="flex flex-wrap gap-4 p-5 border-b border-slate-100 bg-slate-50" id="messageSearchFilter">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
                <select id="messageFilterStatus"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 cursor-pointer transition-all">
                    <option value="">All statuses</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table" id="messagesTable">
                <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Recipient</th>
                            <th>Creator</th>
                            <th>Created</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages ?? [] as $msg)
                            @php /** @var \App\Models\WishMessages $msg */ @endphp
                            @php
                                $msgStatus = $msg->is_published ? 'published' : ($msg->expires_at && $msg->expires_at->isPast() ? 'expired' : 'draft');
                                $msgContent = $msg->message ?? $msg->wish_message ?? $msg->greeting ?? '';
                                $msgRowNum = method_exists($messages ?? null, 'currentPage') ? (($messages->currentPage() - 1) * $messages->perPage() + $loop->iteration) : $loop->iteration;
                            @endphp
                            <tr class="message-data-row" style="cursor: pointer;" onclick="toggleMessageDetails('{{ $msg->id }}')" data-status="{{ $msgStatus }}" data-title="{{ strtolower(e($msg->title ?? '')) }}" data-recipient="{{ strtolower(e($msg->recipient_name ?? $msg->recipient_special_name ?? '')) }}" data-creator="{{ strtolower(e($msg->user->username ?? '')) }}" data-msg-id="{{ $msg->id }}">
                                <td>{{ $msgRowNum }}</td>
                                <td>{{ $msg->title ?? '-' }}</td>
                                <td class="blur-sensitive">{{ $msg->recipient_name ?? $msg->recipient_special_name ?? '-' }}</td>
                                <td>{{ $msg->user ? $msg->user->username : '-' }}</td>
                                <td>{{ $msg->created_at ? $msg->created_at->format('Y-m-d') : '-' }}</td>
                                <td>{{ $msg->views_count ?? 0 }}</td>
                                <td>{{ $msg->is_published ? 'Published' : ($msg->expires_at && $msg->expires_at->isPast() ? 'Expired' : 'Draft') }}</td>
                                <td class="action-buttons">
                                    <button type="button" class="action-btn view btn-view-message" data-message-id="{{ $msg->id }}" data-preview-url="{{ route('admin.messages.preview', $msg->id) }}" data-edit-action="{{ route('admin.messages.update', $msg->id) }}" data-title="{{ e($msg->title ?? '') }}" data-content="{{ e($msgContent) }}" data-recipient="{{ e($msg->recipient_name ?? '') }}" data-status="{{ $msg->is_published ? '1' : '0' }}">View</button>
                                    <button type="button" class="action-btn edit btn-edit-message" data-message-id="{{ $msg->id }}" data-edit-action="{{ route('admin.messages.update', $msg->id) }}" data-title="{{ e($msg->title ?? '') }}" data-content="{{ e($msgContent) }}" data-status="{{ $msg->is_published ? '1' : '0' }}">Edit</button>
                                    <form method="POST" action="{{ route('admin.messages.destroy', $msg->id) }}" class="d-inline" onsubmit="event.preventDefault(); showAdminConfirm('Delete this message permanently?', () => this.submit());">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            
                            @php
                                $imagesSize = 0;
                                $audioSize = 0;
                                $media = $msg->mediaFiles;
                                if ($media) {
                                    if ($media->recipient_image) {
                                        $path = storage_path('app/public/' . $media->recipient_image);
                                        $imagesSize += file_exists($path) ? filesize($path) : 0;
                                    }
                                    if ($media->background_music) {
                                        $path = storage_path('app/public/' . $media->background_music);
                                        $audioSize += file_exists($path) ? filesize($path) : 0;
                                    }
                                }
                                $totalStorage = $imagesSize + $audioSize;
                        
                                $formatBytes = function($bytes) {
                                    if ($bytes == 0) return '0.00 B';
                                    $s = array('B', 'KB', 'MB', 'GB');
                                    $e = floor(log($bytes, 1024));
                                    return round($bytes/pow(1024, $e), 2).' '.$s[$e];
                                };
                        
                                $link = isset($msg->generatedLinks) ? $msg->generatedLinks->where('is_active', true)->sortByDesc('created_at')->first() : null;
                            @endphp
                            
                            <tr id="details-row-{{ $msg->id }}" class="message-detail-dropdown-row" style="display: none;">
                                <td colspan="8" style="padding: 0; border-bottom: 2px solid var(--primary);">
                                    <div style="background: var(--bg-body); padding: 20px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                                            
                                            <!-- Template & Link Info -->
                                            <div class="db-card" style="padding: 16px;">
                                                <h5 style="margin-top: 0; margin-bottom: 12px; color: var(--text-main); font-size: 0.95rem;">
                                                    <i class="fas fa-info-circle text-primary"></i> General Info
                                                </h5>
                                                <div style="margin-bottom: 8px; font-size: 0.9rem;">
                                                    <strong style="color: var(--text-muted);">Template:</strong> 
                                                    <span style="color: var(--text-main); font-weight: 500;">{{ $msg->template ? $msg->template->template_name : 'None' }}</span>
                                                </div>
                                                <div style="margin-bottom: 8px; font-size: 0.9rem;">
                                                    <strong style="color: var(--text-muted);">Sharing Stats:</strong> 
                                                    @php
                                                        $sharingStatus = 'Not sent';
                                                        $sharingStatusColor = 'var(--text-muted)';
                                                        if (($msg->views_count ?? 0) > 0) {
                                                            $sharingStatus = 'Viewed';
                                                            $sharingStatusColor = '#10b981'; // success
                                                        } elseif (isset($msg->shareSends) && $msg->shareSends->count() > 0) {
                                                            if ($msg->shareSends->where('status', 'sent')->count() > 0) {
                                                                $sharingStatus = 'Sent';
                                                                $sharingStatusColor = '#3b82f6'; // primary
                                                            } elseif ($msg->shareSends->where('status', 'scheduled')->count() > 0) {
                                                                $sharingStatus = 'Scheduled';
                                                                $sharingStatusColor = '#f59e0b'; // warning
                                                            }
                                                        }
                                                    @endphp
                                                    <span style="color: {{ $sharingStatusColor }}; font-weight: 500;">{{ $sharingStatus }}</span>
                                                </div>
                                                <div style="font-size: 0.9rem;">
                                                    <strong style="color: var(--text-muted);">Generated Link:</strong> 
                                                    <span style="color: var(--text-main); font-weight: 500;">
                                                        @if($link)
                                                            <a href="{{ $link->generated_url }}" target="_blank" style="color: var(--primary); text-decoration: none;">{{ $link->generated_url }}</a>
                                                        @else
                                                            No active link
                                                        @endif
                                                    </span>
                                                </div>
                                                <div style="font-size: 0.9rem; margin-top: 8px;">
                                                    <strong style="color: var(--text-muted);">Vault Status:</strong> 
                                                    @if($msg->is_vaulted)
                                                        <span style="color: var(--primary); font-weight: 500;">Vaulted</span>
                                                        <span style="color: var(--text-muted); font-size: 0.8rem; margin-left: 4px;">
                                                            ({{ $msg->specific_vault_pin ? 'Specific' : 'General' }})
                                                        </span>
                                                    @else
                                                        <span style="color: var(--text-muted); font-weight: 500;">Not Vaulted</span>
                                                    @endif
                                                </div>
                                            </div>
                        
                                            <!-- Media Files Info -->
                                            <div class="db-card" style="padding: 16px;">
                                                <h5 style="margin-top: 0; margin-bottom: 12px; color: var(--text-main); font-size: 0.95rem;">
                                                    <i class="fas fa-photo-video text-secondary"></i> Media Files
                                                </h5>
                                                <div style="max-height: 200px; overflow-y: auto; font-size: 0.85rem; padding-right: 5px;">
                                                    @if($media)
                                                        <ul style="margin: 0; padding-left: 20px; color: var(--text-muted); list-style-type: none; padding-left: 0;">
                                                            @if($media->recipient_image)
                                                                <li style="margin-bottom: 12px; background: var(--bg-body); padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                                                                    <div>
                                                                        <i class="fas fa-image text-primary" style="color: #3b82f6;"></i> <strong style="color: var(--text-main);">Recipient Image</strong> <span style="color: var(--text-muted); font-size: 0.8rem;">({{ $formatBytes($imagesSize) }})</span>
                                                                    </div>
                                                                    <a href="{{ Storage::url($media->recipient_image) }}" target="_blank" class="btn btn-sm" style="background-color: #3b82f6; color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; border: none;">
                                                                        View <i class="fas fa-external-link-alt" style="font-size: 0.75rem; margin-left: 4px;"></i>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li style="margin-bottom: 12px; background: var(--bg-body); padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; opacity: 0.7;">
                                                                    <div>
                                                                        <i class="fas fa-image" style="color: #9ca3af;"></i> <strong style="color: var(--text-main);">Recipient Image</strong> <span style="color: var(--text-muted); font-size: 0.8rem;">(Not uploaded)</span>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                            
                                                            @if($media->background_music)
                                                                <li style="margin-bottom: 0; background: var(--bg-body); padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                                                                    <div>
                                                                        <i class="fas fa-music text-secondary" style="color: #6366f1;"></i> <strong style="color: var(--text-main);">Background Audio</strong> 
                                                                        @if(!str_starts_with($media->background_music, 'http'))
                                                                            <span style="color: var(--text-muted); font-size: 0.8rem;">({{ $formatBytes($audioSize) }})</span>
                                                                        @else
                                                                            <span style="color: var(--text-muted); font-size: 0.8rem;">(Spotify Link)</span>
                                                                        @endif
                                                                    </div>
                                                                    <a href="{{ str_starts_with($media->background_music, 'http') ? $media->background_music : Storage::url($media->background_music) }}" target="_blank" class="btn btn-sm" style="background-color: #6366f1; color: white; padding: 4px 10px; border-radius: 4px; text-decoration: none; border: none;">
                                                                        Listen <i class="fas fa-headphones" style="font-size: 0.75rem; margin-left: 4px;"></i>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li style="margin-bottom: 0; background: var(--bg-body); padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; opacity: 0.7;">
                                                                    <div>
                                                                        <i class="fas fa-music" style="color: #9ca3af;"></i> <strong style="color: var(--text-main);">Background Audio</strong> <span style="color: var(--text-muted); font-size: 0.8rem;">(Not uploaded)</span>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    @else
                                                        <span style="color: var(--text-muted);">No media attached.</span>
                                                    @endif
                                                </div>
                                            </div>
                        
                                            <!-- Storage Info -->
                                            <div class="db-card" style="padding: 16px;">
                                                <h5 style="margin-top: 0; margin-bottom: 12px; color: var(--text-main); font-size: 0.95rem;">
                                                    <i class="fas fa-hdd text-success"></i> Storage Usage
                                                </h5>
                                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.9rem;">
                                                    <span style="color: var(--text-muted);">Images:</span>
                                                    <strong style="color: var(--text-main);">{{ $formatBytes($imagesSize) }}</strong>
                                                </div>
                                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.9rem;">
                                                    <span style="color: var(--text-muted);">Audio:</span>
                                                    <strong style="color: var(--text-main);">{{ $formatBytes($audioSize) }}</strong>
                                                </div>
                                                <hr style="border-color: var(--border-color); margin: 8px 0;">
                                                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 0.95rem;">
                                                    <span style="color: var(--text-main);">Total:</span>
                                                    <strong class="text-danger">{{ $formatBytes($totalStorage) }}</strong>
                                                </div>
                                            </div>
                        
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="message-row-empty">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-700 m-0 mb-1">No Messages Found</h4>
                                    <p class="text-sm font-medium text-slate-500 m-0">No messages have been created yet.</p>
                                </td>
                            </tr>
                        @endforelse
                        <tr class="message-row-no-results" style="display:none;"><td colspan="8" class="text-center text-muted">No messages match your search or filters.</td></tr>
                    </tbody>
                </table>
            </div>
            @if(isset($messages) && method_exists($messages, 'currentPage') && $messages->lastPage() > 0)
            <div class="custom-pagination">
                <div class="pagination-info">
                    Showing page {{ $messages->currentPage() }} of {{ $messages->lastPage() }}
                </div>
                <div class="pagination-btns">
                    <button type="button" class="pagination-btn" aria-label="Previous" @if($messages->onFirstPage()) disabled @endif onclick="@if(!$messages->onFirstPage()) location.href='{{ $messages->previousPageUrl() }}'; @endif"><i class="fas fa-chevron-left"></i> Previous</button>
                    <button type="button" class="pagination-btn" aria-label="Next" @if(!$messages->hasMorePages()) disabled @endif onclick="@if($messages->hasMorePages()) location.href='{{ $messages->nextPageUrl() }}'; @endif">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function toggleMessageDetails(id) {
        if (event.target.closest('.action-btn') || event.target.closest('form')) {
            return;
        }

        const targetRow = document.getElementById(`details-row-${id}`);
        const isCurrentlyVisible = targetRow.style.display !== 'none';

        // Hide all others
        document.querySelectorAll('.message-detail-dropdown-row').forEach(row => {
            row.style.display = 'none';
        });

        // Toggle clicked one
        if (!isCurrentlyVisible) {
            targetRow.style.display = 'table-row';
        }
    }
</script>
@endsection
