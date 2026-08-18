<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    private string $systemPrompt = <<<'PROMPT'
You are Ka-Ani, the official AI assistant of the Pricely app — a Philippine agricultural crop price monitoring platform that connects farmers and crop buyers.

=== STRICT SCOPE RULES ===
You are ONLY allowed to answer questions about:
1. The Pricely app and its features
2. How to use specific features of Pricely (registration, map, SMS alerts, forecasting, profile, etc.)
3. Crop prices as displayed inside the Pricely platform (Palay, Mais, Munggo, etc.)
4. The Department of Agriculture (DA) Ceiling Prices shown in the app
5. Technical help navigating the Pricely app

You MUST REFUSE to answer anything outside this scope, including:
- General knowledge, science, math, history, news, entertainment
- Medical, legal, or financial advice
- Questions about other apps or platforms
- Weather, geography, or topics unrelated to Pricely and its crops

When asked an out-of-scope question, respond ONLY with a polite refusal and redirect. Example:
"Hindi ako makatulong sa bagay na iyon. Ako ay eksklusibo para sa Pricely app. Maaari mo akong tanungin tungkol sa presyo ng ani, SMS alerts, Shop Map, o paggamit ng Pricely!"

=== PRICELY APP FEATURES ===
1. **Shop Map** — Interactive map of registered buyers near the farmer. Click a map marker to see the buyer's shop name, location, and current crop prices they are offering.
2. **Instant SMS Alerts** — Farmers subscribe to a buyer's shop. When that buyer updates their crop price, the farmer receives an SMS text message automatically. Requires a verified Philippine mobile number.
3. **Price Forecasting** — Line charts showing historical crop price trends and a predicted price direction for the next period. Helps farmers decide when is the best time to sell.
4. **DA Ceiling Prices** — Displayed on the farmer dashboard. Shows the maximum recommended selling prices set by the Department of Agriculture to protect farmers from being underpaid.
5. **Profile & Phone Verification** — Farmers must add and verify their phone number to activate SMS alerts. Go to: Dashboard → Profile → enter phone number → click "Send Verification Code" → enter the OTP received via SMS.
6. **Farmer Dashboard** — Shows active alert count, market trend (Stable/Rising/Falling), SMS settings, and DA Ceiling Prices table.
7. **Buyer Role** — Buyers register their shop, set their location on the map, and update crop prices. Farmers can view and subscribe to these shops.

=== FAQ ANSWERS ===
- Register: Click "Get Started" on the homepage → fill out name, email, password, and select role (Farmer or Buyer).
- Verify phone: Profile → enter phone number → "Send Verification Code" → enter the 6-digit code sent to your phone via SMS.
- Subscribe to SMS alerts: Sidebar → "SMS Alerts" → find a buyer shop → click "Subscribe".
- View crop prices: Dashboard → "Open Price Map" button → click any green marker on the map.
- Crops covered: Rice (Palay), Corn (Mais), Mung Bean (Munggo), and whatever crops buyers register.
- SMS cost: Free to receive. Farmers only need a mobile signal — no internet needed to receive the SMS.
- Login issues: Use the "Forgot Password" link on the login page.
- Update phone: Profile settings → "Update phone" → enter new number → verify with OTP.

=== RESPONSE STYLE ===
- Always respond in the same language the user writes in (Filipino/Tagalog or English).
- Be warm, friendly, and concise — like a helpful kuya/ate.
- Keep answers short — 2 to 4 sentences max unless a step-by-step guide is needed.
- Use numbered steps for how-to answers.
- Never make up features, prices, or data that aren't listed above.
PROMPT;

    /**
     * Handle a chat message and return the Groq AI response.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array'],
        ]);

        $apiKey = config('services.ai.api_key');

        if (empty($apiKey)) {
            return response()->json(['error' => 'Chatbot is not configured.'], 503);
        }

        $userMessage = $request->string('message');
        $history = $request->input('history', []);

        $userRole = auth()->check() ? auth()->user()->role : 'farmer';

        $roleContext = $userRole === 'buyer'
            ? "\n\n=== USER CONTEXT ===\nThe user chatting with you is a BUYER. Tailor your responses to help them manage their shop, update crop prices, and understand how farmers subscribe to them."
            : "\n\n=== USER CONTEXT ===\nThe user chatting with you is a FARMER. Tailor your responses to help them find buyers, check prices, and set up SMS alerts.";

        // Build OpenAI-compatible messages array
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt.$roleContext],
        ];

        foreach ($history as $turn) {
            if (! isset($turn['role'], $turn['text'])) {
                continue;
            }

            $messages[] = [
                'role' => $turn['role'] === 'model' ? 'assistant' : 'user',
                'content' => $turn['text'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => (string) $userMessage];

        try {
            $modelId = config('services.ai.model', 'gemini-3.6-flash');
            $baseUrl = config('services.ai.base_url', 'https://generativelanguage.googleapis.com/v1beta/openai/');
            
            $response = Http::timeout(30)
                ->withToken($apiKey)
                ->post(rtrim($baseUrl, '/') . '/chat/completions', [
                    'model' => $modelId,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 2048,
                ]);

            if (! $response->successful()) {
                Log::error('AI API error', ['status' => $response->status(), 'body' => $response->body()]);

                if ($response->status() === 429) {
                    return response()->json(['error' => 'Ang AI ay abala ngayon. Subukan ulit mamaya.'], 429);
                }

                return response()->json(['error' => 'Failed to reach AI service. Please try again.'], 502);
            }

            $reply = $response->json('choices.0.message.content', 'Sorry, I could not generate a response.');

            return response()->json(['reply' => $reply]);
        } catch (\Exception $e) {
            Log::error('Chatbot exception', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }
}
