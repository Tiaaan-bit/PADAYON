<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsService
{
    private const API_URL = 'https://unismsapi.com/api/sms';
    
    public function send(string $phone, string $message): bool
    {
        if (! config('services.sms.enabled')) {
            return false;
        }

        if (config('services.sms.provider') !== 'unisms') {
            Log::warning('SMS provider is not configured as UniSMS.');

            return false;
        }

        $secretKey = config('services.sms.api_key');

        if (blank($secretKey)) {
            Log::warning('UniSMS secret key is missing.');

            return false;
        }

        $recipient = $this->formatPhilippineNumber($phone);

        if ($recipient === null) {
            Log::warning('UniSMS: invalid Philippine mobile number.');

            return false;
        }

        if (blank(trim($message))) {
            Log::warning('UniSMS: SMS message cannot be empty.');

            return false;
        }

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->acceptJson()
                ->asJson()
                ->timeout(30)
                ->post(self::API_URL, [
                    'recipient' => $recipient,
                    'content' => $message,
                    'sender_id' => config(
                        'services.sms.sender_name',
                        'UniSMS'
                    ),
                ]);

            $body = $response->json();

            if (! $response->successful()) {
                Log::warning('UniSMS API request failed.', [
                    'http_status' => $response->status(),
                    'response' => $body ?? $response->body(),
                ]);
            
                return false;
            }

            Log::info('UniSMS API response received.', [
                'http_status' => $response->status(),
                'response' => $body ?? $response->body(),
            ]);

            // HTTP success means the API responded successfully.
            // Actual delivery must be verified using UniSMS status/report.
            return true;
        } catch (Throwable $e) {
            Log::error('UniSMS request exception.', [
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

        return preg_match('/^\+639\d{9}$/', $phone)
            ? $phone
            : null;
    }
}
