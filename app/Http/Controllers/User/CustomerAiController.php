<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\User\CustomerAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomerAiController extends Controller
{
    public function __construct(private readonly CustomerAiService $ai) {}

    public function index(): View
    {
        return view('user.ai.index');
    }

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:10'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $messages = $validated['messages'];

        if (end($messages)['role'] !== 'user') {
            return response()->json(
                [
                    'message' => 'Please send a message first.',
                ],
                422,
            );
        }

        try {
            return response()->json([
                'reply' => $this->ai->reply($request->user(), $messages),
            ]);
        } catch (Throwable $exception) {
            Log::error('Customer AI request failed.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return response()->json(
                [
                    'message' => 'Aira is temporarily unavailable. Please try again.',
                ],
                503,
            );
        }
    }
}
