@extends('layouts.user')

@section('title', 'Padayon Massage Center - Notifications')

@section('content')

<div class="min-h-screen p-4 sm:p-6 lg:p-8">
    <div class="max-w-5xl mx-auto">


    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                Notifications
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Your latest appointment updates, promotions, and alerts.
            </p>
        </div>

        @if ($unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('user.notifications.readAll') }}">
                @csrf

                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-[#849753] text-white text-sm font-medium hover:bg-[#6F4E37] transition"
                >
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- ========================= --}}
    {{-- SERVICE PROMOTIONS --}}
    {{-- ========================= --}}

    <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-gray-800">
                    Service Promotions
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Latest announcements from the administrator.
                </p>
            </div>

            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-[#849753]/10 text-[#849753]">
                {{ $posts->count() }}
            </span>
        </div>

        <div class="divide-y divide-gray-100">

            @forelse($posts as $post)

                <article class="px-5 py-5">

                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                        <div class="flex-1 min-w-0">

                            <div class="flex items-center gap-2 mb-2">

                                <span class="w-2.5 h-2.5 rounded-full bg-[#849753]"></span>

                                <h3 class="font-semibold text-gray-800">
                                    {{ $post->title }}
                                </h3>

                            </div>

                            @if ($post->content)
                                <p class="text-sm text-gray-600 whitespace-pre-line">
                                    {{ $post->content }}
                                </p>
                            @endif

                            @if ($post->published_at)
                                <p class="text-xs text-gray-400 mt-3">
                                    Posted {{ $post->published_at->diffForHumans() }}
                                </p>
                            @endif

                        </div>

                        @if ($post->published_at)
                            <time class="shrink-0 text-xs text-gray-400">
                                {{ $post->published_at->format('M d, Y h:i A') }}
                            </time>
                        @endif

                    </div>

                </article>

            @empty

                <div class="px-5 py-10 text-center text-gray-400">
                    No service promotions or announcements yet.
                </div>

            @endforelse

        </div>

    </section>


    {{-- ========================= --}}
    {{-- UNREAD NOTIFICATIONS --}}
    {{-- ========================= --}}

    <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">

            <h2 class="font-semibold text-gray-800">
                Unread Notifications
            </h2>

            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-[#849753]/10 text-[#849753]">
                {{ $unreadNotifications->count() }}
            </span>

        </div>

        <div class="divide-y divide-gray-100">

            @forelse($unreadNotifications as $notification)

                <div class="px-5 py-5 border-b border-gray-100 last:border-b-0 bg-[#849753]/5">

                    <div class="flex flex-col sm:flex-row items-start justify-between gap-4">

                        <div class="flex-1 min-w-0">

                            <div class="flex items-center gap-2 mb-2">

                                <span class="w-2.5 h-2.5 rounded-full bg-[#849753]"></span>

                                <p class="font-semibold text-gray-800">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </p>

                            </div>

                            <p class="text-sm text-gray-600 mb-3">
                                {{ $notification->data['message'] ?? '' }}
                            </p>

                            <div class="grid sm:grid-cols-2 gap-x-6 gap-y-1 text-sm text-gray-500">

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Service:
                                    </span>

                                    {{ $notification->data['service'] ?? 'N/A' }}
                                </p>

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Therapist:
                                    </span>

                                    {{ $notification->data['therapist'] ?? 'N/A' }}
                                </p>

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Date:
                                    </span>

                                    {{ $notification->data['date'] ?? 'N/A' }}
                                </p>

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Time:
                                    </span>

                                    {{ $notification->data['time'] ?? 'N/A' }}
                                </p>

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Add-on:
                                    </span>

                                    {{ $notification->data['addon'] ?? 'None' }}
                                </p>

                            </div>

                            <p class="text-xs text-gray-400 mt-3">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>

                        </div>


                        {{-- Actions --}}
                        <div class="flex sm:flex-col items-center sm:items-end gap-3">

                            <form
                                method="POST"
                                action="{{ route('user.notifications.read', $notification->id) }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="text-sm font-medium text-[#849753] hover:underline whitespace-nowrap"
                                >
                                    Mark as read
                                </button>
                            </form>


                            <form
                                method="POST"
                                action="{{ route('user.notifications.destroy', $notification->id) }}"
                                onsubmit="return confirm('Delete this notification?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-sm font-medium text-red-500 hover:underline whitespace-nowrap"
                                >
                                    Delete
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            @empty

                <div class="px-5 py-10 text-center text-gray-400">
                    No unread notifications.
                </div>

            @endforelse

        </div>

    </section>


    {{-- ========================= --}}
    {{-- READ NOTIFICATIONS --}}
    {{-- ========================= --}}

    <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">

            <h2 class="font-semibold text-gray-800">
                Read Notifications
            </h2>

            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">
                {{ $readNotifications->count() }}
            </span>

        </div>


        <div class="divide-y divide-gray-100">

            @forelse($readNotifications as $notification)

                <div class="px-5 py-5 border-b border-gray-100 last:border-b-0">

                    <div class="flex flex-col sm:flex-row items-start justify-between gap-4">

                        <div class="flex-1 min-w-0">

                            <p class="font-semibold text-gray-700">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </p>

                            <p class="text-sm text-gray-600 mb-3">
                                {{ $notification->data['message'] ?? '' }}
                            </p>


                            <div class="grid sm:grid-cols-2 gap-x-6 gap-y-1 text-sm text-gray-500">

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Service:
                                    </span>

                                    {{ $notification->data['service'] ?? 'N/A' }}
                                </p>

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Therapist:
                                    </span>

                                    {{ $notification->data['therapist'] ?? 'N/A' }}
                                </p>

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Date:
                                    </span>

                                    {{ $notification->data['date'] ?? 'N/A' }}
                                </p>

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Time:
                                    </span>

                                    {{ $notification->data['time'] ?? 'N/A' }}
                                </p>

                                <p>
                                    <span class="font-medium text-gray-700">
                                        Add-on:
                                    </span>

                                    {{ $notification->data['addon'] ?? 'None' }}
                                </p>

                            </div>


                            <p class="text-xs text-gray-400 mt-3">
                                Read {{ $notification->read_at?->diffForHumans() }}
                            </p>

                        </div>


                        {{-- Delete --}}
                        <form
                            method="POST"
                            action="{{ route('user.notifications.destroy', $notification->id) }}"
                            onsubmit="return confirm('Delete this notification?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="text-sm font-medium text-red-500 hover:underline whitespace-nowrap"
                            >
                                Delete
                            </button>
                        </form>

                    </div>

                </div>

            @empty

                <div class="px-5 py-10 text-center text-gray-400">
                    No read notifications.
                </div>

            @endforelse

        </div>

    </section>

</div>


</div>

@endsection
