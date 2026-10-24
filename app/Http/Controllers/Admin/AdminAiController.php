<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminAiAnalyticsService;
use App\Services\Admin\AdminAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AdminAiController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::guard('web')->check(), 401);

        return view('admin.ai.index');
    }

    public function chat(Request $request, AdminAiAnalyticsService $analytics, AdminAiService $ai): JsonResponse
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(
                [
                    'message' => 'Your admin session has expired. Please sign in again.',
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
                fn(array $message) => [
                    'role' => $message['role'],
                    'content' => trim($message['content']),
                ],
            )
            ->values()
            ->all();

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

        if (end($messages)['role'] !== 'user') {
            return response()->json(
                [
                    'message' => 'Please send a message to continue.',
                ],
                422,
            );
        }

        try {
            $question = end($messages)['content'];
            $context = $analytics->contextForQuestion($question);
            $reply = $ai->reply($messages, $context);

            return response()->json([
                'reply' => $reply,
            ]);
        } catch (Throwable $exception) {
            Log::error('Admin AI request failed.', [
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
