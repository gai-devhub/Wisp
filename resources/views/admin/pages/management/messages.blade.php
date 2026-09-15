@extends('admin.base-admin')

@section('admin-section', 'messages')

@section('content')
<div class="content-section active" id="messages">
    <!-- Header Section -->
    <div class="admin-card-header-bar">
        <h2 class="admin-page-title">
            <i class="fas fa-envelope text-primary"></i>
            Message Management
        </h2>
    </div>

    <div class="db-card overflow-hidden">
        {{-- Filter bar --}}
        <div class="message-filter-box" id="messageSearchFilter">
            <div class="w-160">
                <select id="messageFilterStatus" class="message-filter-select">
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
                            <tr class="message-data-row cursor-pointer" onclick="toggleMessageDetails(event, '{{ $msg->id }}')" data-status="{{ $msgStatus }}" data-title="{{ strtolower(e($msg->title ?? '')) }}" data-recipient="{{ strtolower(e($msg->recipient_name ?? $msg->recipient_special_name ?? '')) }}" data-creator="{{ strtolower(e($msg->user->username ?? '')) }}" data-msg-id="{{ $msg->id }}">
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
                            
                            <tr id="details-row-{{ $msg->id }}" class="message-detail-dropdown-row">
                                <td colspan="8" class="message-detail-dropdown-cell">
                                    <div class="message-detail-dropdown-wrapper">
                                        <div class="message-detail-grid">
                                            
                                            <!-- Template & Link Info -->
                                            <div class="db-card user-detail-card">
                                                <h5>
                                                    <i class="fas fa-info-circle text-primary"></i> General Info
                                                </h5>
                                                <div class="message-detail-item">
                                                    <strong>Template:</strong> 
                                                    <span>{{ $msg->template ? $msg->template->template_name : 'None' }}</span>
                                                </div>
                                                <div class="message-detail-item">
                                                    <strong>Sharing Stats:</strong> 
                                                    @php
                                                        $sharingStatus = 'Not sent';
                                                        $sharingStatusColor = 'var(--text-muted)';
                                                        if (($msg->views_count ?? 0) > 0) {
                                                            $sharingStatus = 'Viewed';
                                                            $sharingStatusColor = 'var(--primary, #E8674A)';
                                                        } elseif (isset($msg->shareSends) && $msg->shareSends->count() > 0) {
                                                            if ($msg->shareSends->where('status', 'sent')->count() > 0) {
                                                                $sharingStatus = 'Sent';
                                                                $sharingStatusColor = 'var(--primary, #E8674A)';
                                                            } elseif ($msg->shareSends->where('status', 'scheduled')->count() > 0) {
                                                                $sharingStatus = 'Scheduled';
                                                                $sharingStatusColor = 'var(--secondary, #F28C76)';
                                                            }
                                                        }
                                                    @endphp
                                                    <span style="color: {{ $sharingStatusColor }};">{{ $sharingStatus }}</span>
                                                </div>
                                                <div class="message-detail-item">
                                                    <strong>Generated Link:</strong> 
                                                    <span>
                                                        @if($link)
                                                             <a href="{{ $link->generated_url }}" target="_blank" class="text-primary" title="{{ $link->generated_url }}">{{ $link->generated_url }}</a>
                                                        @else
                                                            No active link
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="message-detail-item margin-top-8">
                                                    <strong>Vault Status:</strong> 
                                                    @if($msg->is_vaulted)
                                                        <span class="text-primary">Vaulted</span>
                                                        <span class="text-muted text-xs">
                                                            ({{ $msg->specific_vault_pin ? 'Specific' : 'General' }})
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Not Vaulted</span>
                                                    @endif
                                                </div>
                                            </div>
                        
                                            <!-- Media Files Info -->
                                            <div class="db-card user-detail-card">
                                                <h5>
                                                    <i class="fas fa-photo-video text-primary"></i> Media Files
                                                </h5>
                                                <div class="max-h-200 overflow-y-auto text-xs pr-1">
                                                    @if($media)
                                                        <ul class="list-none p-0 m-0">
                                                            @if($media->recipient_image)
                                                                <li class="admin-github-badge mb-2">
                                                                    <div>
                                                                        <i class="fas fa-image text-primary"></i> <strong class="text-dark">Recipient Image</strong> <span class="text-muted text-xs">({{ $formatBytes($imagesSize) }})</span>
                                                                    </div>
                                                                    <a href="{{ s3_url($media->recipient_image) }}" target="_blank" class="admin-btn admin-btn-primary btn-sm">
                                                                        View <i class="fas fa-external-link-alt text-xs"></i>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li class="admin-github-badge mb-2 opacity-70">
                                                                    <div>
                                                                        <i class="fas fa-image text-muted"></i> <strong class="text-dark">Recipient Image</strong> <span class="text-muted text-xs">(Not uploaded)</span>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                            
                                                            @if($media->background_music)
                                                                <li class="admin-github-badge">
                                                                    <div>
                                                                        <i class="fas fa-music text-primary"></i> <strong class="text-dark">Background Audio</strong> 
                                                                        @if(!str_starts_with($media->background_music, 'http'))
                                                                            <span class="text-muted text-xs">({{ $formatBytes($audioSize) }})</span>
                                                                        @else
                                                                            <span class="text-muted text-xs">(Spotify Link)</span>
                                                                        @endif
                                                                    </div>
                                                                    <a href="{{ str_starts_with($media->background_music, 'http') ? $media->background_music : s3_url($media->background_music) }}" target="_blank" class="admin-btn admin-btn-primary btn-sm">
                                                                        Listen <i class="fas fa-headphones text-xs"></i>
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li class="admin-github-badge opacity-70">
                                                                    <div>
                                                                        <i class="fas fa-music text-muted"></i> <strong class="text-dark">Background Audio</strong> <span class="text-muted text-xs">(Not uploaded)</span>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">No media attached.</span>
                                                    @endif
                                                </div>
                                            </div>
                        
                                            <!-- Storage Info -->
                                            <div class="db-card user-detail-card">
                                                <h5>
                                                    <i class="fas fa-hdd text-primary"></i> Storage Usage
                                                </h5>
                                                <div class="message-detail-item flex-between">
                                                    <span class="text-muted">Images:</span>
                                                    <strong class="text-dark">{{ $formatBytes($imagesSize) }}</strong>
                                                </div>
                                                <div class="message-detail-item flex-between">
                                                    <span class="text-muted">Audio:</span>
                                                    <strong class="text-dark">{{ $formatBytes($audioSize) }}</strong>
                                                </div>
                                                <hr class="border-t my-2">
                                                <div class="message-detail-item flex-between font-700">
                                                    <span class="text-dark">Total:</span>
                                                    <strong class="text-danger">{{ $formatBytes($totalStorage) }}</strong>
                                                </div>
                                            </div>
                        
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="message-row-empty">
                                <td colspan="8" class="admin-table-empty">
                                    <div class="ai-icon-avatar margin-0-auto mb-4 text-2xl">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <h4 class="admin-table-empty-title">No Messages Found</h4>
                                    <p class="text-sm font-medium text-slate-500 m-0">No messages have been created yet.</p>
                                </td>
                            </tr>
                        @endforelse
                        <tr class="message-row-no-results hidden"><td colspan="8" class="admin-table-empty">No messages match your search or filters.</td></tr>
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
    function toggleMessageDetails(event, id) {
        if (event && (event.target.closest('.action-buttons') || event.target.closest('.action-btn') || event.target.closest('form') || event.target.closest('button') || event.target.closest('a'))) {
            return;
        }

        const targetRow = document.getElementById(`details-row-${id}`);
        if (!targetRow) return;

        const isCurrentlyVisible = targetRow.classList.contains('show') || targetRow.style.display === 'table-row';

        // Hide all other message detail rows
        document.querySelectorAll('.message-detail-dropdown-row').forEach(row => {
            row.classList.remove('show');
            row.style.display = 'none';
        });

        // Toggle clicked one
        if (!isCurrentlyVisible) {
            targetRow.classList.add('show');
            targetRow.style.display = 'table-row';
        }
    }
</script>
@endsection
