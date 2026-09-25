@props([
    'type' => 'success',
    'title' => null,
    'message' => null,
])

@php
    $sessionMessage = session($type);
    $message = $message ?? $sessionMessage;
@endphp

@if ($message)
    <div
        x-data="{ show: true }"
        x-init="
            setTimeout(() => {
                show = false;
            }, 5000);
        "
        x-show="show"
        x-cloak

        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 translate-x-4"
        x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"

        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 translate-x-0"
        x-transition:leave-end="opacity-0 translate-y-4 translate-x-4"

        class="fixed bottom-5 right-5 z-9999 w-[calc(100%-2.5rem)] max-w-md"
    >
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl">

            <div class="flex items-start gap-4 p-5">

                {{-- Icon --}}
                <div
                    @class([
                        'flex h-12 w-12 shrink-0 items-center justify-center rounded-full',
                        'bg-green-100 text-green-600' => $type === 'success',
                        'bg-red-100 text-red-600' => $type === 'error',
                        'bg-yellow-100 text-yellow-600' => $type === 'warning',
                        'bg-blue-100 text-blue-600' => $type === 'info',
                    ])
                >

                    @if ($type === 'success')
                        {{-- Success --}}
                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>

                    @elseif ($type === 'error')
                        {{-- Error --}}
                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>

                    @elseif ($type === 'warning')
                        {{-- Warning --}}
                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v3m0 4h.01M10.29 3.86l-7.82 14a2 2 0 001.74 3h15.58a2 2 0 001.74-3l-7.82-14a2 2 0 00-3.48 0z"
                            />
                        </svg>

                    @elseif ($type === 'info')
                        {{-- Info --}}
                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"
                            />
                        </svg>
                    @endif

                </div>

                {{-- Message --}}
                <div class="min-w-0 flex-1">

                    <h2 class="text-base font-bold text-gray-800">
                        {{ $title ?? match ($type) {
                            'success' => 'Success',
                            'error' => 'Error',
                            'warning' => 'Warning',
                            'info' => 'Information',
                            default => 'Notification',
                        } }}
                    </h2>

                    <p class="mt-1 text-sm leading-5 text-gray-600">
                        {{ $message }}
                    </p>

                </div>

                {{-- Close --}}
                <button
                    type="button"
                    @click="show = false"
                    class="shrink-0 rounded-lg p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

            </div>

            {{-- Progress --}}
            <div class="h-1 w-full bg-gray-200">
                <div
                    @class([
                        'h-full',
                        'bg-[#849753]' => $type === 'success',
                        'bg-red-500' => $type === 'error',
                        'bg-yellow-500' => $type === 'warning',
                        'bg-blue-500' => $type === 'info',
                    ])
                    style="animation: toastProgress 5s linear forwards;"
                ></div>
            </div>

        </div>
    </div>

    <style>
        @keyframes toastProgress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>
@endif

