<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use App\Models\Therapists;
use App\Services\Therapist\TherapistAiService;
use App\Services\Therapist\TherapistAppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TherapistAiController extends Controller
{
    public function index(): View
    {
        $therapist = Auth::guard('therapist')->user();

        abort_unless($therapist instanceof Therapists, 401);

        return view('therapist.ai.index', compact('therapist'));
    }

    public function chat(Request $request, TherapistAiService $ai, TherapistAppointmentService $appointments): JsonResponse
    {
        /** @var Therapists|null $therapist */
        $therapist = Auth::guard('therapist')->user();

        if (!$therapist instanceof Therapists) {
            return response()->json(
                [
                    'message' => 'Your therapist session has expired. Please sign in again.',
                ],
                401,
            );
        }

        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:10'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $messages = collect($validated['messages'])
            ->map(
                fn(array $message): array => [
                    'role' => $message['role'],
                    'content' => trim($message['content']),
                ],
            )
            ->values()
            ->all();

        // Reject empty messages, including whitespace-only messages.
        foreach ($messages as $message) {
            if ($message['content'] === '') {
                return response()->json(
                    [
                        'message' => 'Please enter a message before sending.',
                    ],
                    422,
                );
            }
        }

        // The most recent message must be the therapist's question.
        $lastMessage = $messages[array_key_last($messages)];

        if ($lastMessage['role'] !== 'user') {
            return response()->json(
                [
                    'message' => 'Please send a message to continue.',
                ],
                422,
            );
        }

        try {
            $appointmentContext = $appointments->contextForQuestion($therapist, $lastMessage['content']);

            $answer = $ai->reply($messages, $appointmentContext);

            return response()->json([
                'reply' => $answer,
            ]);
        } catch (Throwable $exception) {
            Log::error('Therapist AI request failed.', [
                'therapist_id' => $therapist->getKey(),
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return response()->json(
                [
                    'message' => 'The AI assistant is temporarily unavailable. Please try again.',
                ],
                503,
            );
        }
    }
}
