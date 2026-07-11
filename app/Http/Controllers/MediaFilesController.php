<?php

namespace App\Http\Controllers;

use App\Models\MediaFiles;
use App\Models\WishMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MediaFilesController extends Controller
{
    /**
     * Get media for a message (so the form can show current image/music when message is selected or edited).
     * User must own the message. Returns JSON with recipient_image_url and background_music_url (or nulls).
     */
    public function show(Request $request)
    {
        $request->validate(['message_id' => 'required|integer']);
        $messageId = (int) $request->input('message_id');
        WishMessages::where('user_id', Auth::id())->findOrFail($messageId);

        $media = MediaFiles::where('user_id', Auth::id())
            ->where('wish_message_id', $messageId)
            ->first();

        $recipientImageUrl = null;
        $backgroundMusicUrl = null;
        $recipientImageName = null;
        $backgroundMusicName = null;
        $appleMusicUrl = null;

        if ($media) {
            if ($media->recipient_image) {
                $recipientImageUrl = '/storage/' . $media->recipient_image;
                $recipientImageName = basename($media->recipient_image);
            }
            if ($media->background_music) {
                if (str_starts_with($media->background_music, 'http')) {
                    $backgroundMusicUrl = $media->background_music;
                    $backgroundMusicName = 'Spotify Track';
                } else {
                    $backgroundMusicUrl = '/storage/' . $media->background_music;
                    $backgroundMusicName = basename($media->background_music);
                }
            }
            if ($media->apple_music_url) {
                $appleMusicUrl = $media->apple_music_url;
            }
        }

        return response()->json([
            'recipient_image_url' => $recipientImageUrl,
            'recipient_image_name' => $recipientImageName,
            'background_music_url' => $backgroundMusicUrl,
            'background_music_name' => $backgroundMusicName,
            'apple_music_url' => $appleMusicUrl,
        ]);
    }

    /**
     * Store or update media (recipient image + background music) for a message.
     * The message must belong to the current user (e.g. a draft they created).
     * User selects a message, optionally uploads image and/or song; both are attached to that message.
     */
    public function store(Request $request)
    {
        if (Auth::user()->isStorageFull()) {
            return redirect()->route('user.media.page')
                ->with('error', 'Your storage is full (1GB limit reached). Please delete some old media files to free up space.')
                ->withInput();
        }

        try {
            $request->validate([
                'wish_message_id' => 'required|integer',
                'recipient_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
                'background_music' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac|max:30720',
                'spotify_url' => 'nullable|url',
                'apple_music_url' => 'nullable|url',
            ], [
                'wish_message_id.required' => 'Please select a message.',
                'recipient_image.image' => 'Recipient image must be an image (jpeg, png, gif, webp).',
                'recipient_image.mimes' => 'Recipient image must be jpeg, jpg, png, gif or webp.',
                'recipient_image.max' => 'Recipient image must not exceed 5MB.',
                'background_music.mimes' => 'Background music must be mp3, wav, ogg, m4a or aac.',
                'background_music.max' => 'Background music must not exceed 30MB.',
                'background_music.uploaded' => 'Background music could not be uploaded. Use a file under 30MB in mp3, wav, ogg, m4a or aac format. If the file is large, the server may need higher upload limits.',
            ]);
        } catch (ValidationException $e) {
            app(\App\Services\NotificationService::class)->record(
                Auth::id(),
                'error',
                'Media save failed',
                $e->getMessage(),
                ['action' => 'media.store', 'errors' => $e->errors()]
            );
            return redirect()->route('user.media.page')
                ->with('error', implode(' ', collect($e->errors())->flatten()->all()))
                ->withErrors($e->errors())
                ->withInput();
        }

        $messageId = (int) $request->input('wish_message_id');
        $message = WishMessages::where('user_id', Auth::id())->find($messageId);
        if (!$message) {
            app(\App\Services\NotificationService::class)->record(
                Auth::id(),
                'error',
                'Media save failed',
                'Selected message not found or access denied.',
                ['action' => 'media.store']
            );
            return redirect()->route('user.media.page')
                ->with('error', 'Selected message not found or access denied.')
                ->withInput();
        }

        $recipientImagePath = null;
        $backgroundMusicPath = null;

        try {
            if ($request->hasFile('recipient_image')) {
                $file = $request->file('recipient_image');
                $recipientImagePath = $file->store('media/recipient-images', 'public');
            }

            if ($request->hasFile('background_music')) {
                $file = $request->file('background_music');
                $backgroundMusicPath = $file->store('media/background-music', 'public');
            } elseif ($request->filled('spotify_url')) {
                $backgroundMusicPath = $request->input('spotify_url');
            }
            
            if ($request->filled('apple_music_url')) {
                $media->apple_music_url = $request->input('apple_music_url');
            }
        } catch (\Throwable $e) {
            app(\App\Services\NotificationService::class)->record(
                Auth::id(),
                'error',
                'Media upload failed',
                $e->getMessage(),
                ['action' => 'media.store']
            );
            return redirect()->route('user.media.page')
                ->with('error', 'Failed to store file. Please try again.')
                ->withInput();
        }

        try {
            $media = MediaFiles::firstOrNew([
                'user_id' => Auth::id(),
                'wish_message_id' => $message->id,
            ]);

            $media->user_id = Auth::id();
            $media->wish_message_id = $message->id;
            if ($recipientImagePath !== null) {
                if ($media->recipient_image) {
                    Storage::disk('public')->delete($media->recipient_image);
                }
                $media->recipient_image = $recipientImagePath;
            }
            if ($backgroundMusicPath !== null) {
                if ($media->background_music && !str_starts_with($media->background_music, 'http')) {
                    Storage::disk('public')->delete($media->background_music);
                }
                $media->background_music = $backgroundMusicPath;
            }
            if ($request->filled('apple_music_url')) {
                $media->apple_music_url = $request->input('apple_music_url');
            }
            $media->save();

            app(\App\Services\NotificationService::class)->record(
                Auth::id(),
                'success',
                'Media saved',
                'Media saved and attached to the message.',
                ['action' => 'media.store', 'wish_message_id' => $message->id]
            );

            return redirect()->route('user.my-messages.page')
                ->with('success', 'Media saved and attached to the message.');
        } catch (\Throwable $e) {
            app(\App\Services\NotificationService::class)->record(
                Auth::id(),
                'error',
                'Media save failed',
                $e->getMessage(),
                ['action' => 'media.store']
            );
            return redirect()->route('user.media.page')
                ->with('error', 'Failed to save media to database. Please try again.')
                ->withInput();
        }
    }

    /**
     * API endpoint to store or update media files via JSON/Multipart.
     */
    public function saveApi(Request $request)
    {
        \Log::info('saveApi payload', $request->all());
        \Log::info('saveApi files', $request->allFiles());
        
        try {
            $request->validate([
                'wish_message_id' => 'required|integer',
                'recipient_image' => 'nullable|image|max:5120',
                'background_music' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,mp4|max:30720',
                'spotify_url' => 'nullable|url',
                'apple_music_url' => 'nullable|url',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => collect($e->errors())->flatten()->first()], 422);
        }

        $messageId = (int) $request->input('wish_message_id');
        $message = WishMessages::where('user_id', Auth::id())->find($messageId);
        
        if (!$message) {
            return response()->json(['message' => 'Selected message not found or access denied.'], 404);
        }

        $media = MediaFiles::firstOrNew([
            'user_id' => Auth::id(),
            'wish_message_id' => $message->id,
        ]);

        if ($request->hasFile('recipient_image')) {
            if ($media->recipient_image) {
                Storage::disk('public')->delete($media->recipient_image);
            }
            $file = $request->file('recipient_image');
            $media->recipient_image = $file->store('media/recipient-images', 'public');
        }

        if ($request->hasFile('background_music')) {
            if ($media->background_music && !str_starts_with($media->background_music, 'http')) {
                Storage::disk('public')->delete($media->background_music);
            }
            $file = $request->file('background_music');
            $media->background_music = $file->store('media/background-music', 'public');
            $media->apple_music_url = null;
        } elseif ($request->filled('spotify_url')) {
            if ($media->background_music && !str_starts_with($media->background_music, 'http')) {
                Storage::disk('public')->delete($media->background_music);
            }
            $media->background_music = $request->input('spotify_url');
            $media->apple_music_url = null;
        }

        if ($request->filled('apple_music_url')) {
            if ($media->background_music && !str_starts_with($media->background_music, 'http')) {
                Storage::disk('public')->delete($media->background_music);
            }
            $media->background_music = null;
            $media->apple_music_url = $request->input('apple_music_url');
        }

        $media->save();

        return response()->json([
            'message' => 'Media saved successfully',
            'media' => $media
        ]);
    }
}
