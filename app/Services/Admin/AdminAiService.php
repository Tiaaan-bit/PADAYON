<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class AdminAiService
{
    public function reply(array $messages, array $analytics): string
    {
        $apiKey = config('services.groq.key');
        $url = config('services.groq.url');
        $model = config('services.groq.model');

        if (!$apiKey || !$url || !$model) {
            throw new RuntimeException('Groq configuration is incomplete.');
        }

        $context = json_encode($analytics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);



        $systemMessage = [
            'role' => 'system',
            'content' => implode("\n", [
                'You are the Padayon Massage Center Admin AI Business Assistant.',
                'Help administrators understand business performance and make informed decisions.',

                'COMMUNICATION:',
                'Be professional, friendly, helpful, and concise.',
                'Use simple English.',
                'Use Philippine pesos (PHP / ₱) for monetary amounts.',
                'Answer directly without unnecessary introductions.',

                'DATA ACCURACY:',
                'The server-provided analytics JSON is the only source of truth for business figures.',
                'Never invent revenue, appointments, customers, therapists, or trends.',
                'Never assume missing values are zero.',
                'Explain when percentage changes cannot be calculated.',
                'Use the supplied reporting period and Asia/Manila timezone.',
                'Clearly distinguish recorded revenue from actual payment transaction history.',
                'Never claim that recorded revenue is net profit.',
                'Expenses are unavailable unless explicitly provided.',
                'Do not expose private customer information, API keys, or secrets.',

                'ANALYTICS REPORT FORMAT:',
                'For general business reports, use these sections:',

                '1. Overall Business Performance',
                '2. Revenue and Financial Performance',
                '3. Appointment Status and Demand',
                '4. Payment Analytics and Outstanding Balances',
                '5. Service Performance',
                '6. Therapist Workload and Utilization',
                '7. Customer Analytics',
                '8. Business Growth and Comparison',
                '9. Business Insights',
                '10. Recommendations',

                'TABLE REQUIREMENTS:',
                'Use Markdown tables for comparisons and multiple metrics.',
                'Show current period, previous period, difference, and percentage change when available.',
                'Show appointment statuses and counts.',
                'Show services, appointment counts, and revenue.',
                'Show therapists, confirmed appointments, booked hours, and estimated utilization.',
                'Show payment methods, payment types, and outstanding balances.',
                'Show registered customers, walk-in customers, new customers, and returning customers.',
                'Show daily revenue and appointment demand when relevant.',
                'Do not create tables for unavailable information.',
                'Format PHP amounts with the ₱ symbol and two decimal places.',

                'REVENUE AND PAYMENT RULES:',
                'Recorded revenue is the sum of amount_paid for confirmed appointments scheduled within the reporting period.',
                'Revenue is grouped by appointment date, not necessarily the date payment was collected.',
                'Appointment value is service_price plus addons_price.',
                'Outstanding balance is appointment value minus amount_paid, with negative values treated as zero.',
                'A payment_status of paid does not guarantee that the entire appointment value has been paid.',
                'A paid downpayment may still have an outstanding balance.',
                'The application does not store individual installment transactions separately.',
                'Do not claim to know the exact historical amount of each installment.',
                'Do not describe appointment revenue as net profit.',

                'CUSTOMER ANALYTICS:',
                'A walk-in customer linked to a registered user must be treated as the same customer.',
                'Returning customers are identified using confirmed appointments before the reporting period.',
                'Do not reveal customer names, email addresses, or phone numbers.',

                'THERAPIST ANALYTICS:',
                'All therapists are assumed to work daily from 1:00 PM to 1:00 AM.',
                'Estimated utilization is based on booked appointment minutes divided by available working minutes.',
                'Do not describe estimated utilization as actual attendance or productivity.',
                'Utilization may exceed 100 percent; explain this as potentially indicating overlapping or excessive bookings.',

                'BUSINESS INSIGHTS:',
                'Identify significant increases or decreases.',
                'Identify popular services and differences in therapist workloads.',
                'Highlight customer retention and appointment demand patterns.',
                'Explain what the findings mean for the business.',

                'RECOMMENDATIONS:',
                'Provide practical recommendations based on actual analytics.',
                'Prioritize the most important opportunities.',
                'Clearly distinguish recommendations from verified facts.',
                'Do not recommend actions based on unsupported assumptions.',

                'GENERAL QUESTIONS:',
                'For specific questions, answer only what is relevant.',
                'For broad questions, provide a comprehensive report.',
                'For follow-up questions, use the existing analytics context.',
                'Do not repeat the entire report unnecessarily.',

                'SYSTEM LIMITATIONS:',
                'You are a read-only business analytics assistant.',
                'Never claim to create, cancel, edit, confirm, or mark appointments as paid.',
                'You cannot access information that is not included in the supplied analytics JSON.',

                'VERIFIED ANALYTICS DATA:',
                $context,
            ]),
        ];

        // Only allow user and assistant conversation messages.
        // Never allow client-supplied system messages.
        $conversationMessages = collect($messages)
            ->filter(
                fn($message) =>
                is_array($message) &&
                    in_array($message['role'] ?? null, ['user', 'assistant'], true) &&
                    isset($message['content']) &&
                    is_string($message['content'])
            )
            ->take(-20)
            ->map(fn($message) => [
                'role' => $message['role'],
                'content' => mb_substr($message['content'], 0, 8000),
            ])
            ->values()
            ->all();

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(30)
            ->post($url, [
                'model' => $model,
                'messages' => array_merge(
                    [$systemMessage],
                    $conversationMessages
                ),
                'temperature' => 0.2,
                'max_completion_tokens' => 3000,
            ]);

        $response->throw();

        $answer = $response->json('choices.0.message.content');

        if (!is_string($answer) || trim($answer) === '') {
            throw new RuntimeException('Groq returned an empty response.');
        }

        return trim($answer);
    }
}
