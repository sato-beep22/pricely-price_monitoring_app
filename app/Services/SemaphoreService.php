<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SemaphoreService
{
    protected string $apiUrl;

    protected string $otpUrl;

    protected ?string $apiKey;

    protected string $senderName;

    protected bool $senderNameActive;

    public function __construct()
    {
        $this->apiUrl = config('services.semaphore.api_url', 'https://api.semaphore.co/api/v4/messages');
        $this->otpUrl = 'https://api.semaphore.co/api/v4/otp';
        $this->apiKey = config('services.semaphore.api_key');
        $this->senderName = config('services.semaphore.sender_name', 'Pricely');

        $activeConfig = config('services.semaphore.sender_name_active', false);
        $this->senderNameActive = filter_var($activeConfig, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Build the base payload for an API call.
     * Includes sendername only when the sender name is active.
     *
     * @return array<string, string>
     */
    protected function basePayload(string $phone, string $message): array
    {
        $payload = [
            'apikey' => $this->apiKey,
            'number' => $phone,
            'message' => $message,
        ];

        if ($this->senderNameActive) {
            $payload['sendername'] = $this->senderName;
        }

        return $payload;
    }

    /**
     * Send an SMS message using the Semaphore API.
     *
     * @param  string  $phone  The recipient's phone number
     * @param  string  $message  The message content
     * @return bool True if successful, false otherwise
     */
    public function sendSms(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::warning("Semaphore API key is not configured. SMS not sent to {$phone}.");

            return false;
        }

        try {
            $response = Http::asForm()->post($this->apiUrl, $this->basePayload($phone, $message));

            if ($response->successful()) {
                $responseData = $response->json();
                $messageId = null;

                if (is_array($responseData) && count($responseData) > 0 && isset($responseData[0]['message_id'])) {
                    $messageId = $responseData[0]['message_id'];
                } elseif (is_array($responseData) && isset($responseData['message_id'])) {
                    $messageId = $responseData['message_id'];
                }

                SmsLog::create([
                    'phone_number' => $phone,
                    'message' => $message,
                    'type' => 'Price Update',
                    'status' => 'Completed',
                    'provider' => 'Semaphore',
                    'message_code' => $messageId,
                    'response_data' => $responseData,
                ]);

                Log::info("SMS sent successfully to {$phone}.", ['response' => $responseData]);

                return true;
            }

            SmsLog::create([
                'phone_number' => $phone,
                'message' => $message,
                'type' => 'Price Update',
                'status' => 'Failed',
                'provider' => 'Semaphore',
                'response_data' => $response->json(),
            ]);

            Log::error("Failed to send SMS to {$phone}.", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            SmsLog::create([
                'phone_number' => $phone,
                'message' => $message,
                'type' => 'Price Update',
                'status' => 'Failed',
                'provider' => 'Semaphore',
                'response_data' => ['error' => $e->getMessage()],
            ]);

            Log::error("Exception while sending SMS to {$phone}.", ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Send an OTP code via SMS.
     *
     * When SEMAPHORE_SENDER_NAME_ACTIVE=true, uses Semaphore's dedicated /otp route
     * (high-priority, bypasses DND). Otherwise falls back to /messages with a shared
     * sender number — sufficient while waiting for sender name approval.
     *
     * @param  string  $phone  The recipient's phone number
     * @param  string  $code  The OTP code to send
     * @return bool True if successful, false otherwise
     */
    public function sendOtp(string $phone, string $code): bool
    {
        if (empty($this->apiKey)) {
            Log::warning("Semaphore API key is not configured. OTP not sent to {$phone}.");

            return false;
        }

        try {
            $message = "Your Pricely verification code is {$code}. It expires in 5 minutes.";
            
            if ($this->senderNameActive) {
                // Use the dedicated OTP route — requires an active sender name.
                $response = Http::asForm()->post($this->otpUrl, array_merge(
                    $this->basePayload($phone, $message),
                    ['code' => $code]
                ));
            } else {
                // Fallback: regular messages route — does not require an active sender name.
                $response = Http::asForm()->post($this->apiUrl, $this->basePayload($phone, $message));
            }

            if ($response->successful()) {
                $responseData = $response->json();
                $messageId = null;

                if (is_array($responseData) && count($responseData) > 0 && isset($responseData[0]['message_id'])) {
                    $messageId = $responseData[0]['message_id'];
                } elseif (is_array($responseData) && isset($responseData['message_id'])) {
                    $messageId = $responseData['message_id'];
                }

                SmsLog::create([
                    'phone_number' => $phone,
                    'message' => $message,
                    'type' => 'OTP Verification',
                    'status' => 'Completed',
                    'provider' => 'Semaphore',
                    'message_code' => $messageId,
                    'response_data' => $responseData,
                ]);

                Log::info("OTP sent successfully to {$phone}.", ['response' => $responseData]);

                return true;
            }

            SmsLog::create([
                'phone_number' => $phone,
                'message' => $message,
                'type' => 'OTP Verification',
                'status' => 'Failed',
                'provider' => 'Semaphore',
                'response_data' => $response->json(),
            ]);

            Log::error("Failed to send OTP to {$phone}.", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            SmsLog::create([
                'phone_number' => $phone,
                'message' => 'Your Pricely verification code is ...',
                'type' => 'OTP Verification',
                'status' => 'Failed',
                'provider' => 'Semaphore',
                'response_data' => ['error' => $e->getMessage()],
            ]);

            Log::error("Exception while sending OTP to {$phone}.", ['error' => $e->getMessage()]);

            return false;
        }
    }
}
