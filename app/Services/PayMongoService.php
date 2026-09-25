<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayMongoService
{
    private string $baseUrl = 'https://api.paymongo.com';

    public function createCheckoutSession(string $referenceNumber, string $description, int $amount, string $successUrl, string $cancelUrl, string $customerName, string $customerEmail): array
    {
        $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
            ->acceptJson()
            ->post($this->baseUrl . '/v2/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [
                            [
                                'name' => $description,
                                'amount' => $amount,
                                'currency' => 'PHP',
                                'quantity' => 1,
                            ],
                        ],

                        'payment_method_types' => ['gcash'],

                        'reference_number' => $referenceNumber,

                        'description' => $description,

                        'billing' => [
                            'name' => $customerName,
                            'email' => $customerEmail,
                        ],

                        'success_url' => $successUrl,

                        'cancel_url' => $cancelUrl,
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('PayMongo Checkout Session creation failed: ' . $response->body());
        }

        return $response->json();
    }
}
