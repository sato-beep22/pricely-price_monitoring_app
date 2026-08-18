<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InfobipService
{
    protected ?string $apiKey;
    protected ?string $baseUrl;
    protected bool $testMode;
    protected string $sender;

    public function __construct()
    {
        $this->apiKey = config('services.infobip.api_key', env('INFOBIP_API_KEY'));
        $this->baseUrl = config('services.infobip.base_url', env('INFOBIP_BASE_URL', 'https://api.infobip.com'));
        $this->testMode = config('services.infobip.test_mode', env('INFOBIP_TEST_MODE', true));
        $this->sender = config('services.infobip.sender', env('INFOBIP_SENDER', 'ServiceSMS'));
    }

    /**
     * Send an OTP code via SMS using Infobip.
     *
     * @param  string  $phone  The recipient's phone number
     * @param  string  $code  The OTP code to send
     * @return bool True if successful, false otherwise
     */
    public function sendOtp(string $phone, string $code): bool
    {
        $message = "Your Pricely verification code is {$code}. It expires in 5 minutes.";

        // If we are in test mode, do not send the actual SMS (save the 15 free messages).
        if ($this->testMode) {
            Log::info("INFOBIP TEST MODE: Fake SMS sent to {$phone}. Code: {$code}");
            return true;
        }

        if (empty($this->apiKey)) {
            Log::warning("Infobip API key is not configured. OTP not sent to {$phone}.");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "App {$this->apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(rtrim($this->baseUrl, '/') . '/sms/2/text/advanced', [
                'messages' => [
                    [
                        'destinations' => [['to' => $phone]],
                        'from' => $this->sender,
                        'text' => $message,
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info("OTP sent successfully via Infobip to {$phone}.", ['response' => $response->json()]);
                return true;
            }

            Log::error("Failed to send OTP via Infobip to {$phone}.", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Exception while sending OTP via Infobip to {$phone}.", ['error' => $e->getMessage()]);
            return false;
        }
    }
    public function sendSms(string $phone, string $message): bool
    {
        if ($this->testMode) {
            Log::info("INFOBIP TEST MODE: Fake SMS sent to {$phone}. Message: {$message}");
            return true;
        }

        if (empty($this->apiKey)) {
            Log::warning("Infobip API key is not configured. SMS not sent to {$phone}.");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "App {$this->apiKey}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(rtrim($this->baseUrl, '/') . '/sms/2/text/advanced', [
                'messages' => [
                    [
                        'destinations' => [['to' => $phone]],
                        'from' => $this->sender,
                        'text' => $message,
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info("SMS sent successfully via Infobip to {$phone}.", ['response' => $response->json()]);
                return true;
            }

            Log::error("Failed to send SMS via Infobip to {$phone}.", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Exception while sending SMS via Infobip to {$phone}.", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
