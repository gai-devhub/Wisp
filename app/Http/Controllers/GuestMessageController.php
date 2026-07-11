<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WishMessages;
use App\Models\Template;
use App\Models\MediaFiles;
use App\Models\GeneratedLinks;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GuestMessageController extends Controller
{
    /**
     * Show the public Try WISP page.
     */
    public function index(Request $request)
    {
        $ip = $request->ip();
        $guestEmail = 'guest-' . str_replace([':', '.'], '-', $ip) . '@wisp.local';
        
        $guestUser = User::where('email', $guestEmail)->first();
        
        $messageCount = 0;
        if ($guestUser) {
            $messageCount = WishMessages::where('user_id', $guestUser->id)->count();
        }

        $themes = array_merge(['view' => 6], \App\Http\Controllers\TemplateGalleryController::THEMES);

        return view('welcome.try', compact('messageCount', 'themes'));
    }

    /**
     * Handle the creation of a guest message.
     */
    public function store(Request $request)
    {
        $ip = $request->ip();
        $guestEmail = 'guest-' . str_replace([':', '.'], '-', $ip) . '@wisp.local';

        $guestUser = User::firstOrCreate(
            ['email' => $guestEmail],
            [
                'name' => 'Guest User',
                'username' => 'guest_' . Str::random(8),
                'password' => bcrypt(Str::random(16)),
                'role' => 'user',
                'status' => 'active'
            ]
        );

        if (!$request->has('message_id')) {
            $messageCount = WishMessages::where('user_id', $guestUser->id)->count();
            
            if ($messageCount >= 2) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'You have reached the maximum of 2 trial messages. Please create an account to send unlimited messages!'], 403);
                }
                return redirect()->back()->with('error', 'You have reached the maximum of 2 trial messages. Please create an account to send unlimited messages!');
            }
        }

        $request->validate([
            'message_type' => 'required|string',
            'title' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_special_name' => 'nullable|string|max:255',
            'greeting' => 'required|string|max:255',
            'message_body' => 'required|string',
            'last_note' => 'nullable|string|max:255',
            'receiving_date' => 'nullable|date',
            'sender_name' => 'required|string|max:255',
            'template_name' => 'required|string',
            'recipient_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
        ]);

        DB::beginTransaction();

        try {
            if ($request->has('message_id') && $request->message_id) {
                // Update existing message
                $wishMessage = WishMessages::where('id', $request->message_id)
                                            ->where('user_id', $guestUser->id)
                                            ->firstOrFail();
                
                $wishMessage->update([
                    'message_type' => $request->message_type,
                    'title' => $request->title,
                    'recipient_name' => $request->recipient_name,
                    'recipient_special_name' => $request->recipient_special_name,
                    'greeting' => $request->greeting,
                    'message' => $request->message_body,
                    'wish_message' => $request->message_body,
                    'last_note' => $request->last_note,
                    'sender_name' => $request->sender_name,
                    'receiving_date' => $request->receiving_date ? Carbon::parse($request->receiving_date) : now(),
                ]);

                // Update Template
                $template = Template::where('wish_message_id', $wishMessage->id)->first();
                if ($template) {
                    $template->update(['template_name' => $request->template_name]);
                } else {
                    Template::create([
                        'user_id' => $guestUser->id,
                        'wish_message_id' => $wishMessage->id,
                        'template_name' => $request->template_name,
                    ]);
                }

                // Update Media File
                $mediaFile = MediaFiles::where('wish_message_id', $wishMessage->id)->first();
                if ($request->hasFile('recipient_image')) {
                    $image = $request->file('recipient_image');
                    $imagePath = 'media/images/' . time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('media/images'), $imagePath);
                    
                    if ($mediaFile) {
                        $mediaFile->update(['recipient_image' => $imagePath]);
                    } else {
                        MediaFiles::create([
                            'user_id' => $guestUser->id,
                            'wish_message_id' => $wishMessage->id,
                            'recipient_image' => $imagePath,
                        ]);
                    }
                }

                // Fetch existing link
                $link = GeneratedLinks::where('wish_message_id', $wishMessage->id)->first();
                $generatedUrl = $link ? $link->generated_url : url('/' . Str::slug($wishMessage->message_type) . '/' . $wishMessage->slug);

            } else {
                // Create new message
                $slug = Str::slug($request->recipient_name . '-' . Str::random(6));

                $wishMessage = WishMessages::create([
                    'user_id' => $guestUser->id,
                    'message_type' => $request->message_type,
                    'title' => $request->title,
                    'recipient_name' => $request->recipient_name,
                    'recipient_special_name' => $request->recipient_special_name,
                    'greeting' => $request->greeting,
                    'message' => $request->message_body,
                    'wish_message' => $request->message_body,
                    'last_note' => $request->last_note,
                    'sender_name' => $request->sender_name,
                    'receiving_date' => $request->receiving_date ? Carbon::parse($request->receiving_date) : now(),
                    'expiry_hours' => 24, // trial messages expire in 24 hrs
                    'slug' => $slug,
                    'is_published' => true,
                ]);

                Template::create([
                    'user_id' => $guestUser->id,
                    'wish_message_id' => $wishMessage->id,
                    'template_name' => $request->template_name,
                ]);

                $imagePath = null;
                if ($request->hasFile('recipient_image')) {
                    $image = $request->file('recipient_image');
                    $imagePath = 'media/images/' . time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('media/images'), $imagePath);
                }

                MediaFiles::create([
                    'user_id' => $guestUser->id,
                    'wish_message_id' => $wishMessage->id,
                    'recipient_image' => $imagePath,
                ]);

                $uniqueCode = Str::random(8);
                $generatedUrl = url('/' . Str::slug($wishMessage->message_type) . '/' . $wishMessage->slug);

                GeneratedLinks::create([
                    'wish_message_id' => $wishMessage->id,
                    'generated_url' => $generatedUrl,
                    'unique_code' => $uniqueCode,
                    'is_active' => true,
                    'expires_at' => now()->addHours(24),
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message_id' => $wishMessage->id,
                    'generated_link' => $generatedUrl,
                    'title' => $wishMessage->title,
                ]);
            }

            return redirect()->back()->with([
                'success' => 'Message saved successfully!',
                'generated_link' => $generatedUrl
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Guest message saving failed: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Something went wrong while saving your message.'], 500);
            }
            
            return redirect()->back()->with('error', 'Something went wrong while saving your message.');
        }
    }
}
