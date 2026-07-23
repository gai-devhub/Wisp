<?php

namespace App\Http\Controllers;

use App\Models\GeneratedLinks;
use App\Models\WishMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GeneratedLinksController extends Controller
{
    /**
     * Generate a shareable link for a message (only when user clicks Generate).
     * Message must belong to the user and be saved (published).
     * URL path uses message type (e.g. /vows/slug, /birthday/slug).
     */
    public function store(Request $request)
    {
        $request->validate([
            'wish_message_id' => 'required|integer',
        ]);

        $message = WishMessages::where('user_id', Auth::id())
            ->find($request->input('wish_message_id'));

        if (!$message) {
            return redirect()->back()
                ->with('error', 'Message not found or access denied.');
        }


        $typeSlug = \Illuminate\Support\Str::slug($message->message_type); // e.g. birthday, vows, condolences
        $path = '/' . $typeSlug . '/' . $message->slug;
        $generatedUrl = url($path);

        $link = GeneratedLinks::updateOrCreate(
            [
                'wish_message_id' => $message->id,
            ],
            [
                'generated_url' => $generatedUrl,
                'unique_code'   => Str::random(12),
                'is_active'     => true,
                'expires_at'    => $message->expires_at,
            ]
        );

        return redirect()->back()
            ->with('success', 'Link generated successfully.')
            ->with('generated_link', $link->generated_url);
    }

    /**
     * Toggle the active status of the latest generated link for a message (API).
     */
    public function toggleLinkStatus(Request $request, $id)
    {
        $message = WishMessages::where('user_id', Auth::id())->findOrFail($id);
        $link = $message->generatedLinks()->latest()->first();

        if (!$link) {
            return response()->json(['success' => false, 'message' => 'No generated link found for this message.'], 404);
        }

        $link->is_active = !$link->is_active;
        $link->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $link->is_active,
            'message' => 'Link status updated successfully.'
        ]);
    }

    /**
     * Permanently delete all generated links for a message (API).
     */
    public function deleteLink($id)
    {
        $message = WishMessages::where('user_id', Auth::id())->findOrFail($id);
        $message->generatedLinks()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Generated link deleted permanently.'
        ]);
    }
}
