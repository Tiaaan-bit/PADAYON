<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use App\Services\TherapistAiService;
use App\Services\TherapistAppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;
use App\Models\Therapists;
use Illuminate\Support\Facades\Auth;

class TherapistAiController extends Controller
{
    public function index(): View
    {
        $therapist = auth('therapist')->user();

        return view('therapist.ai.index', compact('therapist'));
    }

    public function chat(Request $request, TherapistAiService $ai, TherapistAppointmentService $appointments): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:10'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $messages = collect($validated['messages'])
            ->map(
                fn(array $message) => [
                    'role' => $message['role'],
                    'content' => trim($message['content']),
                ],
            )
            ->values()
            ->all();

        $lastMessage = end($messages);

        if (!$lastMessage || $lastMessage['role'] !== 'user' || trim($lastMessage['content']) === '') {
            return response()->json(
                [
                    'message' => 'Please send a message to continue.',
                ],
                422,
            );
        }

        try {
            $appointmentContext = null;
            $question = mb_strtolower($lastMessage['content']);

            $asksAboutToday = preg_match('/\b(today|this day)\b/', $question) && preg_match('/\b(appointment|appointments|schedule|bookings)\b/', $question);

            if ($asksAboutToday) {
    /** @var Therapists|null $therapist */
    $therapist = Auth::guard('therapist')->user();

    if (! $therapist instanceof Therapists) {
        abort(401);
    }

    $records = $appointments->getTodaysAppointments($therapist);

    $appointmentContext = [
        'date' => now('Asia/Manila')->format('F j, Y'),
        'timezone' => 'Asia/Manila',
        'total_appointments' => count($records),
        'appointments' => $records,
    ];
}

            $answer = $ai->reply($messages, $appointmentContext);

            return response()->json([
                'reply' => $answer,
            ]);
        } catch (Throwable $exception) {
            Log::error('Therapist AI request failed.', [
    'therapist_id' => auth('therapist')->id(),
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
