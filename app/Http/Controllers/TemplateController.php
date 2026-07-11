<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\WishMessages;
use App\Models\Template;
use Carbon\Carbon;

class TemplateController extends Controller
{
    /** Template keys for views (view-1..6) plus theme templates (template.Theme.template-N). */
    public static function getAllTemplateKeys(): array
    {
        $keys = [];
        $themes = [
            'view' => 6,
            'Aurora' => 14,
            'Casual' => 22,
            'Confetti' => 12,
            'Minimal' => 18,
            'Garden' => 10,
            'Glitter' => 8,
            'Romance' => 15,
        ];
        foreach ($themes as $theme => $count) {
            for ($n = 1; $n <= $count; $n++) {
                $keys[] = 'template.' . $theme . '.template-' . $n;
            }
        }
        return $keys;
    }

    /**
     * Return all templates as JSON for the API.
     */
    public function listTemplates()
    {
        return response()->json([
            'templates' => self::getAllTemplateKeys()
        ]);
    }

    // chooser view removed per requirement

    /**
     * Render a single template preview with message data.
     */
    public function preview(Request $request)
    {
        $messageId = (int) $request->query('message_id');
        $template = $request->query('template'); // e.g., view-1 .. view-6

        if (!$messageId || !$template) {
            return response('Missing parameters', 400);
        }

        $message = Auth::user()->role === 'admin'
            ? WishMessages::findOrFail($messageId)
            : WishMessages::where('user_id', Auth::id())->findOrFail($messageId);

        $mediaFiles = \App\Models\MediaFiles::where('wish_message_id', $message->id)->first();

        // Ensure template blade exists — view-N keys use the view/ subfolder
        if (preg_match('/^view-([1-6])$/', $template, $m)) {
            $viewName = 'components.template.view.template-' . $m[1];
        } elseif (preg_match('/^view\.view-([1-6])$/', $template, $m)) {
            $viewName = 'components.template.view.template-' . $m[1];
        } elseif (preg_match('/^template\.view\.view-([1-6])$/', $template, $m)) {
            $viewName = 'components.template.view.template-' . $m[1];
        } else {
            $viewName = 'components.' . $template;
        }
        if (!View::exists($viewName)) {
            return response()
                ->view('components.template-unavailable', [], 404);
        }

        return view($viewName, [ 'message' => $message, 'mediaFiles' => $mediaFiles ]);
    }

    /**
     * Resolve message_id by receiving_date (Y-m-d) for current user.
     */
    public function findByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $message = WishMessages::where('user_id', Auth::id())
            ->whereDate('receiving_date', $request->query('date'))
            ->latest('created_at')
            ->first();

        if (!$message) {
            return response()->json(['found' => false], 404);
        }

        return response()->json([
            'found' => true,
            'message_id' => $message->id,
        ]);
    }

    /**
     * Get the newest message for the current user.
     */
    public function latest(Request $request)
    {
        $message = WishMessages::where('user_id', Auth::id())
            ->latest('created_at')
            ->first();
        if (!$message) {
            return response()->json(['found' => false], 404);
        }
        return response()->json([
            'found' => true,
            'message_id' => $message->id,
        ]);
    }

    /**
     * Save selected template for a message.
     */
    public function select(Request $request)
    {
        $request->validate([
            'message_id' => 'required|integer',
            'template_name' => 'required|string',
        ]);

        $templateName = $request->input('template_name');
        // Resolve view name — view-N keys use the view/ subfolder
        if (preg_match('/^view-([1-6])$/', $templateName, $m)) {
            $viewName = 'components.template.view.template-' . $m[1];
        } elseif (preg_match('/^view\.view-([1-6])$/', $templateName, $m)) {
            $viewName = 'components.template.view.template-' . $m[1];
        } elseif (preg_match('/^template\.view\.view-([1-6])$/', $templateName, $m)) {
            $viewName = 'components.template.view.template-' . $m[1];
        } else {
            $viewName = 'components.' . $templateName;
        }
        if (!View::exists($viewName)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Invalid template selected.'], 400);
            }
            return redirect()->route('user.my-messages.page')
                ->with('error', 'Invalid template selected.')
                ->withInput();
        }

        $message = Auth::user()->role === 'admin'
            ? WishMessages::findOrFail($request->input('message_id'))
            : WishMessages::where('user_id', Auth::id())->findOrFail($request->input('message_id'));

        $userId = $message->user_id;

        Template::updateOrCreate(
            [
                'user_id' => $userId,
                'wish_message_id' => $message->id,
            ],
            [
                'template_name' => $request->input('template_name'),
            ]
        );

        // On selecting a template, publish the message and set expiry window from receiving_date
        $activationStart = Carbon::parse($message->receiving_date)->startOfDay();
        $message->is_published = true;
        // If current time before activation start, set expires_at from activation start; else from now
        $base = now()->lt($activationStart) ? $activationStart : now();
        $message->expires_at = $base->copy()->addHours($message->expiry_hours ?? 24);
        $message->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template saved successfully.'
            ]);
        }

        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.page')
                ->with('success', 'Template saved for message.');
        }
        return redirect()->route('user.my-messages.page')
            ->with('success', 'Message saved! It is no longer a draft.');
    }
}
