<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TemplateGalleryController extends Controller
{
    /** @var array Theme name => count */
    public const THEMES = [
        'view' => 6,
        'aurora' => 14,
        'casual' => 22,
        'confetti' => 12,
        'minimal' => 18,
        'garden' => 10,
        // 'glitter' => 8,
        // 'romance' => 15,
    ];

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
            // 'Glitter' => 8,
            // 'Romance' => 15,
        ];
        foreach ($themes as $theme => $count) {
            for ($n = 1; $n <= $count; $n++) {
                $keys[] = 'template.' . $theme . '.template-' . $n;
            }
        }
        return $keys;
    }


    /**
     * Create a demo message object for template previews (no DB).
     */
    protected function getDemoMessage(): object
    {
        $msg = (object) [
            'title' => "Sarah's 30th Birthday",
            'recipient_name' => 'Sarah',
            'recipient_special_name' => 'Sarah',
            'greeting' => 'Dear Sarah,',
            'message' => "Wishing you an incredible birthday filled with joy, laughter, and all the things that make you smile. You deserve the very best today and always!\n\nHere's to another year of wonderful memories.",
            'last_note' => 'With love, your friends at WISP',
            'receiving_date' => now(),
        ];
        // Add message_display accessor for templates that use it
        $msg->message_display = nl2br(e($msg->message));
        return $msg;
    }

    /**
     * Public template gallery — no auth required.
     */
    public function index()
    {
        return view('welcome.template-gallery', [
            'themes' => self::THEMES,
        ]);
    }

    /**
     * Public template preview — no auth required.
     * Renders template with demo message data.
     */
    public function preview(Request $request, string $template)
    {
        // Normalize: view-1..6 or Aurora/template-1 etc
        $viewName = $this->resolveViewName($template);
        if (!$viewName || !View::exists($viewName)) {
            abort(404, 'Template not found');
        }

        if ($request->has('message_id')) {
            $message = \App\Models\WishMessages::find($request->query('message_id'));
            if ($message) {
                $message->message_display = nl2br(e($message->wish_message ?? $message->message));
                $mediaFiles = \App\Models\MediaFiles::where('wish_message_id', $message->id)->first();
                return view($viewName, [
                    'message' => $message,
                    'mediaFiles' => $mediaFiles,
                ]);
            }
        }

        $message = $this->getDemoMessage();
        $mediaFiles = null; // No custom media in demo

        return view($viewName, [
            'message' => $message,
            'mediaFiles' => $mediaFiles,
        ]);
    }

    protected function resolveViewName(string $template): ?string
    {
        // view-1, view-2, ... view-6
        if (preg_match('/^view-([1-6])$/', $template, $m)) {
            return 'components.template.view.template-' . $m[1];
        }
        // aurora-1, confetti-3, minimal-5, etc.
        if (preg_match('/^(aurora|casual|confetti|minimal|garden|glitter|romance)-(\d+)$/i', $template, $m)) {
            $theme = ucfirst(strtolower($m[1]));
            $num = (int) $m[2];
            $max = self::THEMES[strtolower($m[1])] ?? 0;
            if ($num >= 1 && $num <= $max) {
                return 'components.template.' . $theme . '.template-' . $num;
            }
        }
        return null;
    }
}
