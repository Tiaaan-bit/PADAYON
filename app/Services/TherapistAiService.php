<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class TherapistAiService
{
    public function reply(
        array $messages,
        ?array $appointmentContext = null
    ): string {
        $apiKey = config('services.groq.key');

        if (! $apiKey) {
            throw new RuntimeException(
                'Groq API key is not configured.'
            );
        }

        $systemMessage = [
            'role' => 'system',
            'content' => implode(' ', [
                'You are Padayon AI, the accessible voice assistant',
                'for therapists at Padayon Massage Center.',
                'Address the therapist in a friendly and respectful way.',
                'Keep answers short, clear, and natural when spoken aloud.',
                'Help with work questions and accessibility guidance.',
                'Never invent appointments, client details, or business records.',
                'Use appointment records only when they are explicitly provided.',
                'Appointment context supplied by the server is the source of truth.',
                'Do not claim to have checked records unless the server supplied them.',
                'Share only information relevant to the authenticated therapist.',
                'Do not provide diagnoses or replace a healthcare professional.',
            ]),
        ];

        $requestMessages = [$systemMessage];

        if ($appointmentContext !== null) {
            $requestMessages[] = [
                'role' => 'system',
                'content' => "Verified appointment context from the server:\n"
                    . json_encode(
                        $appointmentContext,
                        JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                    )
                    . "\nUse these records to answer the therapist. "
                    . "If there are no appointments, say so clearly. "
                    . "Do not invent missing details.",
            ];
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(30)
            ->post(config('services.groq.url'), [
                'model' => config('services.groq.model'),
                'messages' => array_merge(
                    $requestMessages,
                    $messages
                ),
                'temperature' => 0.3,
                'max_completion_tokens' => 400,
            ]);

        $response->throw();

        $answer = $response->json(
            'choices.0.message.content'
        );

        if (! is_string($answer) || trim($answer) === '') {
            throw new RuntimeException(
                'Groq returned an empty response.'
            );
        }

        return trim($answer);
    }
}
