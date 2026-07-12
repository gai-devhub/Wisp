<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AiChatMessage;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    /**
     * Generate a message using ChatGPT (OpenAI) or Gemini.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'user_message' => 'required|string|max:2000',
            'history' => 'nullable|array',
            'history.*.role' => 'required|string|in:user,assistant,model',
            'history.*.content' => 'required|string',
        ]);
        $provider = config('services.ai_provider', 'groq');

        if ($provider === 'gemini') {
            return $this->generateWithGemini($request);
        }

        return $this->generateWithGroq($request);
    }

    protected function buildSystemPrompt(Request $request): string
    {
        $prompt = "You are WISP AI, a helpful and warm assistant that helps users write genuine messages for cards and greetings. ";
        $prompt .= "You must gather the following information before drafting the final message:\n";
        $prompt .= "1. Occasion or Topic\n";
        $prompt .= "2. Recipient Name\n";
        $prompt .= "3. Relationship between the sender and recipient\n";
        $prompt .= "4. Number of paragraphs desired\n\n";
        $prompt .= "If the user just says hello or greets you, greet them back warmly and ask what kind of message they want to write today. Do NOT say 'Sure' or 'Got it' to a simple greeting.\n";
        $prompt .= "If the user asks a general question about you (like 'who are you', 'what can you do', 'who created you'), politely answer their question first before gently steering them back to the message creation process. Specifically, if they ask who created you, playfully mention that you were brought to life by the brilliant minds at GAI Corp and are proudly owned by the one and only Gilbert Asare!\n";
        $prompt .= "IMPORTANT: You must ask for these 4 pieces of information ONE BY ONE sequentially, not all at once. If you are missing any of these, ask a short, friendly question to get the NEXT missing piece. Do NOT ask for multiple pieces in the same message. Do NOT draft the message yet.\n";
        $prompt .= "Once you have ALL 4 pieces of information, you MUST draft the message with the requested number of paragraphs. Be sure to include relevant and warm emojis in the message! Output your final response STRICTLY as a JSON object (do not wrap in markdown or add extra text) with the following structure:\n";
        $prompt .= '{"type": "final_message", "title": "[A short title for the message]", "recipient_name": "[Recipient Name]", "body": "[The drafted message]"}' . "\n\n";
        $prompt .= "Do not output any JSON until you are ready to draft the final message.\n";
        $prompt .= "IMPORTANT: Under NO circumstances should you attempt to use or call any tools or functions. Always respond directly in plain text (or JSON if drafting the final message).";
        return $prompt;
    }

    protected function generateWithGemini(Request $request)
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'error' => 'Gemini API key is not configured. Set GEMINI_API_KEY in .env',
            ], 503);
        }

        $systemPrompt = $this->buildSystemPrompt($request);
        $contents = [];
        if ($request->has('history')) {
            foreach ($request->input('history') as $msg) {
                $role = $msg['role'] === 'assistant' ? 'model' : $msg['role'];
                $contents[] = ['role' => $role, 'parts' => [['text' => $msg['content']]]];
            }
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $request->input('user_message')]]];

        $configuredModel = config('services.gemini.model', 'gemini-2.0-flash');
        $modelsToTry = array_unique(array_merge(
            [$configuredModel],
            ['gemini-2.0-flash', 'gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-1.5-flash-8b', 'gemini-pro']
        ));

        $lastError = null;
        $lastStatus = 400;

        foreach ($modelsToTry as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

            try {
                $response = Http::timeout(30)
                    ->post($url, [
                        'systemInstruction' => [
                            'parts' => [['text' => $systemPrompt]]
                        ],
                        'contents' => $contents,
                        'generationConfig' => [
                            'maxOutputTokens' => 800,
                            'temperature' => 0.8,
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    if (empty($text) && !empty($data['candidates'][0]['content']['parts'])) {
                        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    }

                    AiChatMessage::create([
                        'user_id' => Auth::id(),
                        'message_type' => 'conversational',
                        'prompt' => json_encode($contents),
                        'response' => (string) $text,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => trim((string) $text),
                    ]);
                }

                $lastError = $response->json('error.message') ?? 'Model not available';
                $lastStatus = $response->status();
                $isQuotaOrRateLimit = $response->status() === 429
                    || stripos((string) $lastError, 'quota') !== false
                    || stripos((string) $lastError, 'limit: 0') !== false
                    || stripos((string) $lastError, 'free_tier') !== false;

                if ($isQuotaOrRateLimit) {
                    $lastError = 'Free-tier limit reached. Add a new key from aistudio.google.com/app/apikey (use “Create API key in new project”) to .env as GEMINI_API_KEY, then run: php artisan config:clear. Until then, try writing a short message from the heart.';
                    $lastStatus = 429;
                    break;
                }
                if ($response->status() !== 404 && $response->status() < 500) {
                    break;
                }
                Log::info('Gemini model not available, trying next', ['model' => $model, 'error' => $lastError]);
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                $lastStatus = 502;
                Log::info('Gemini request failed for model', ['model' => $model, 'error' => $lastError]);
            }
        }

        Log::warning('Gemini API error (all models tried)', ['error' => $lastError]);
        $status = $lastStatus >= 500 ? 502 : ($lastStatus === 429 ? 429 : 400);
        return response()->json([
            'success' => false,
            'error' => $lastError ?: 'AI service error. Please try again.',
        ], $status);
    }



    protected function generateWithGroq(Request $request)
    {
        $apiKey = config('services.groq.api_key');
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'error' => 'Groq API key is not configured. Set GROQ_API_KEY in .env',
            ], 503);
        }

        $systemPrompt = $this->buildSystemPrompt($request);
        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        
        if ($request->has('history')) {
            foreach ($request->input('history') as $msg) {
                $role = $msg['role'] === 'model' ? 'assistant' : $msg['role'];
                $messages[] = ['role' => $role, 'content' => $msg['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $request->input('user_message')];

        $configuredModel = config('services.groq.model', 'openai/gpt-oss-20b');
        $modelsToTry = array_unique(array_merge(
            [$configuredModel],
            ['openai/gpt-oss-20b', 'openai/gpt-oss-120b', 'qwen/qwen3.6-27b', 'mixtral-8x7b-32768', 'gemma2-9b-it']
        ));

        $lastError = null;
        $lastStatus = 400;

        foreach ($modelsToTry as $model) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(30)
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => $model,
                        'messages' => $messages,
                        'max_tokens' => 800,
                        'temperature' => 0.8,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['choices'][0]['message']['content'] ?? '';

                    AiChatMessage::create([
                        'user_id' => Auth::id(),
                        'message_type' => 'conversational',
                        'prompt' => json_encode($messages),
                        'response' => $text,
                        'tokens_used' => $data['usage']['total_tokens'] ?? null,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => trim($text),
                    ]);
                }

                $lastError = $response->json('error.message') ?? 'Model not available';
                $lastStatus = $response->status();

                $isQuotaOrRateLimit = $response->status() === 429
                    || stripos((string) $lastError, 'quota') !== false
                    || stripos((string) $lastError, 'rate limit') !== false
                    || stripos((string) $lastError, 'too many requests') !== false;
                
                $isToolError = stripos((string) $lastError, 'Tool choice is none') !== false
                    || stripos((string) $response->json('error.code'), 'tool_use_failed') !== false;

                if ($isQuotaOrRateLimit) {
                    Log::info('Groq rate limit hit, trying next model', ['model' => $model, 'error' => $lastError]);
                } else if ($isToolError) {
                    Log::info('Groq hallucinated a tool call, trying next model', ['model' => $model, 'error' => $lastError]);
                } else if ($response->status() !== 404 && $response->status() < 500) {
                    break;
                } else {
                    Log::info('Groq model not available, trying next', ['model' => $model, 'error' => $lastError]);
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                $lastStatus = 502;
                Log::info('Groq request failed for model', ['model' => $model, 'error' => $lastError]);
            }
        }

        Log::warning('Groq API error (all models tried)', ['error' => $lastError]);
        $status = $lastStatus >= 500 ? 502 : ($lastStatus === 429 ? 429 : 400);
        return response()->json([
            'success' => false,
            'error' => $lastError ?: 'AI service error. Please try again.',
        ], $status);
    }

    /**
     * Display the AI Assistant page for the user.
     */
    public function page(Request $request)
    {
        $userSettings = \App\Models\UserSettings::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();

        return view('user.pages.ai.assistant', [
            'backUrl' => route('user.page'),
            'userSettings' => $userSettings,
        ]);
    }
}
