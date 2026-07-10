<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SemaphoreService
{
    protected string $apiUrl;
    protected ?string $apiKey;
    protected string $senderName;

    public function __construct()
    {
        $this->apiUrl = config('services.semaphore.api_url', 'https://api.semaphore.co/api/v4/messages');
        $this->apiKey = config('services.semaphore.api_key');
        $this->senderName = config('services.semaphore.sender_name', 'Pricely');
    }

    /**
     * Send an SMS message using the Semaphore API.
     *
     * @param string $phone The recipient's phone number
     * @param string $message The message content
     * @return bool True if successful, false otherwise
     */
    public function sendSms(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::warning("Semaphore API key is not configured. SMS not sent to {$phone}: {$message}");
            return false;
        }

        try {
            $response = Http::asForm()->post($this->apiUrl, [
                'apikey' => $this->apiKey,
                'number' => $phone,
                'message' => $message,
                'sendername' => $this->senderName,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent successfully to {$phone}.");
                return true;
            }

            Log::error("Failed to send SMS to {$phone}.", ['response' => $response->json()]);
            return false;
        } catch (\Exception $e) {
            Log::error("Exception while sending SMS to {$phone}.", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
