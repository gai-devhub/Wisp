@props(['icon' => 'fas fa-inbox', 'message' => 'No data found.', 'colspan' => null])

@if($colspan)
    <tr>
        <td colspan="{{ $colspan }}" class="empty-state-column">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="{{ $icon }}"></i>
                </div>
                <p class="empty-state-text">{{ $message }}</p>
            </div>
        </td>
    </tr>
@else
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="{{ $icon }}"></i>
        </div>
        <p class="empty-state-text">{{ $message }}</p>
    </div>
@endif
