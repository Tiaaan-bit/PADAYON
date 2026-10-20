<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsService
{
    private const TEXTBEE_API_URL = 'https://api.textbee.dev/api/v1/gateway/send-sms';

    public function send(string $phone, string $message): bool
    {
        if (!config('services.sms.enabled')) {
            Log::info('SMS sending is disabled.');
            return false;
        }

        if (config('services.sms.provider') !== 'textbee') {
            Log::warning('SMS provider is not configured as TextBee.');
            return false;
        }

        $apiKey = config('services.sms.textbee_api_key');
        $deviceId = config('services.sms.textbee_device_id');

        if (blank($apiKey) || blank($deviceId)) {
            Log::warning('TextBee API key or device ID is missing.');
            return false;
        }

        $recipient = $this->formatPhilippineNumber($phone);

        if ($recipient === null) {
            Log::warning('TextBee: invalid Philippine mobile number.');
            return false;
        }

        if (blank(trim($message))) {
            Log::warning('TextBee: SMS message cannot be empty.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])
                ->acceptJson()
                ->asJson()
                ->timeout(30)
                ->post(self::TEXTBEE_API_URL, [
                    'deviceId' => $deviceId,
                    'recipients' => [$recipient],
                    'message' => $message,
                ]);

            if (!$response->successful()) {
                Log::warning('TextBee API request failed.', [
                    'http_status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                ]);

                return false;
            }

            Log::info('TextBee accepted SMS request.', [
                'http_status' => $response->status(),
                'response' => $response->json(),
            ]);

            // A successful HTTP response does not guarantee
            // that the SMS was delivered to the recipient.
            return true;
        } catch (Throwable $e) {
            Log::error('TextBee SMS request exception.', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function formatPhilippineNumber(string $phone): ?string
    {
        $phone = preg_replace('/[\s\-()]/', '', trim($phone));

        if (str_starts_with($phone, '+63')) {
            // Already in international format.
        } elseif (str_starts_with($phone, '63')) {
            $phone = '+' . $phone;
        } elseif (str_starts_with($phone, '09')) {
            $phone = '+63' . substr($phone, 1);
        } elseif (str_starts_with($phone, '9')) {
            $phone = '+63' . $phone;
        } else {
            return null;
        }

        return preg_match('/^\+639\d{9}$/', $phone) ? $phone : null;
    }
}
