<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IprogService
{
    protected string $apiUrl;
    protected ?string $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('services.iprog.api_url', 'https://www.iprogsms.com/api/v1/sms_messages');
        $this->apiToken = config('services.iprog.api_token');
    }

    /**
     * Send an SMS message using the IPROG SMS API.
     *
     * @param  string  $phone  The recipient's phone number
     * @param  string  $message  The message content
     * @return bool True if successful, false otherwise
     */
    public function sendSms(string $phone, string $message): bool
    {
        if (empty($this->apiToken)) {
            Log::warning("IPROG API token is not configured. SMS not sent to {$phone}.");
            return false;
        }

        try {
            $response = Http::asForm()->post($this->apiUrl, [
                'api_token' => $this->apiToken,
                'phone_number' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent successfully via IPROG to {$phone}.", ['response' => $response->json()]);
                return true;
            }

            Log::error("Failed to send SMS via IPROG to {$phone}.", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Exception while sending SMS via IPROG to {$phone}.", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send an OTP code via SMS.
     *
     * @param  string  $phone  The recipient's phone number
     * @param  string  $code  The OTP code to send
     * @return bool True if successful, false otherwise
     */
    public function sendOtp(string $phone, string $code): bool
    {
        $message = "Your Pricely verification code is {$code}. It expires in 5 minutes.";
        return $this->sendSms($phone, $message);
    }
}
