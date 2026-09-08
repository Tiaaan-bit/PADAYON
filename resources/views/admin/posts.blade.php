@extends('layouts.admin')

@section('title', 'Padayon Massage Center - Announcement')

@section('content')

    <div class="lg:ml-64 min-h-screen flex flex-col pb-20 lg:pb-0">

        <div class="flex-1 p-4 sm:p-6">

            <div class="max-w-5xl mx-auto">

                {{-- ========================================= --}}
                {{-- PAGE HEADER --}}
                {{-- ========================================= --}}

                <div class="mb-8">

                    <h1 class="text-3xl font-bold text-gray-800">
                        Service Promotions
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Create, edit, and manage service promotions and announcements.
                    </p>

                </div>


                {{-- ========================================= --}}
                {{-- SUCCESS MESSAGE --}}
                {{-- ========================================= --}}

                @if(session('success'))

                    <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>

                @endif


                {{-- ========================================= --}}
                {{-- ERROR MESSAGES --}}
                {{-- ========================================= --}}

                @if($errors->any())

                    <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg">

                        <ul class="list-disc list-inside text-sm">

                            @foreach($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                {{-- ========================================= --}}
                {{-- CREATE NEW POST BUTTON --}}
                {{-- ========================================= --}}

                <div class="mb-8">

                    <button
                        type="button"
                        onclick="openCreatePostModal()"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-[#849753] hover:bg-[#6F4E37] text-white font-semibold rounded-lg transition"
                    >

                        <span class="text-xl">
                            +
                        </span>

                        Create New Post

                    </button>

                </div>


                {{-- ========================================= --}}
                {{-- CREATE POST MODAL --}}
                {{-- ========================================= --}}

                <div
                    id="createPostModal"
                    class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto"
                    aria-labelledby="create-post-title"
                    role="dialog"
                    aria-modal="true"
                >

                    {{-- ========================================= --}}
                    {{-- DARK BACKGROUND OVERLAY --}}
                    {{-- ========================================= --}}

                    <div
                        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                        onclick="closeCreatePostModal()"
                    ></div>


                    {{-- ========================================= --}}
                    {{-- MODAL CONTAINER --}}
                    {{-- ========================================= --}}

                    <div class="relative min-h-screen flex items-center justify-center p-4">

                        <div
                            class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto"
                            onclick="event.stopPropagation()"
                        >

                            {{-- ========================================= --}}
                            {{-- MODAL HEADER --}}
                            {{-- ========================================= --}}

                            <div class="flex items-start justify-between p-6 border-b border-gray-100">

                                <div>

                                    <h2
                                        id="create-post-title"
                                        class="text-xl font-bold text-gray-800"
                                    >
                                        Create New Post
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Add a title, description, and date and time for the promotion.
                                    </p>

                                </div>


                                {{-- Close X --}}

                                <button
                                    type="button"
                                    onclick="closeCreatePostModal()"
                                    class="ml-4 text-gray-400 hover:text-gray-700 text-3xl font-bold leading-none transition"
                                    aria-label="Close"
                                >
                                    &times;
                                </button>

                            </div>


                            {{-- ========================================= --}}
                            {{-- CREATE POST FORM --}}
                            {{-- ========================================= --}}

                            <form
                                method="POST"
                                action="{{ route('admin.posts.store') }}"
                                class="p-6 space-y-6"
                            >

                                @csrf


                                {{-- ========================================= --}}
                                {{-- TITLE --}}
                                {{-- ========================================= --}}

                                <div>

                                    <label
                                        for="title"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Title
                                    </label>

                                    <input
                                        type="text"
                                        name="title"
                                        id="title"
                                        value="{{ old('title') }}"
                                        placeholder="Example: 20% Off Our Bed Massage Services"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]"
                                    >

                                    @error('title')

                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>


                                {{-- ========================================= --}}
                                {{-- CONTENT --}}
                                {{-- ========================================= --}}

                                <div>

                                    <label
                                        for="content"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Description
                                    </label>

                                    <textarea
                                        name="content"
                                        id="content"
                                        rows="5"
                                        placeholder="Describe the service promotion..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]"
                                    >{{ old('content') }}</textarea>

                                    @error('content')

                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>


                                {{-- ========================================= --}}
                                {{-- DATE AND TIME --}}
                                {{-- ========================================= --}}

                                <div>

                                    <label
                                        for="published_at"
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Date and Time
                                    </label>

                                    <input
                                        type="datetime-local"
                                        name="published_at"
                                        id="published_at"
                                        value="{{ old('published_at') }}"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]"
                                    >

                                    @error('published_at')

                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>


                                {{-- ========================================= --}}
                                {{-- MODAL BUTTONS --}}
                                {{-- ========================================= --}}

                                <div class="flex flex-col sm:flex-row gap-3 pt-2">

                                    {{-- Publish --}}

                                    <button
                                        type="submit"
                                        class="flex-1 py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg transition-all"
                                    >
                                        Publish Post
                                    </button>


                                    {{-- Cancel --}}

                                    <button
                                        type="button"
                                        onclick="closeCreatePostModal()"
                                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition"
                                    >
                                        Cancel
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- EXISTING POSTS HEADING --}}
                {{-- ========================================= --}}

                <div class="mb-4">

                    <h2 class="text-2xl font-bold text-gray-800">
                        Existing Posts
                    </h2>

                </div>


                {{-- ========================================= --}}
                {{-- EXISTING POSTS --}}
                {{-- ========================================= --}}

                <div class="space-y-5">

                    @forelse($posts as $post)

                        <div class="bg-white rounded-xl shadow p-6">


                            {{-- ========================================= --}}
                            {{-- POST INFORMATION --}}
                            {{-- ========================================= --}}

                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                                <div class="min-w-0">

                                    <h3 class="text-xl font-bold text-gray-800 wrap-break-word">
                                        {{ $post->title }}
                                    </h3>

                                    @if($post->content)

                                        <p class="text-gray-600 mt-2 whitespace-pre-line wrap-break-word">
                                            {{ $post->content }}
                                        </p>

                                    @endif

                                </div>


                                {{-- Published date --}}

                                @if($post->published_at)

                                    <span class="text-sm text-gray-500 whitespace-nowrap">
                                        {{ $post->published_at->format('M d, Y h:i A') }}
                                    </span>

                                @endif

                            </div>


                            {{-- ========================================= --}}
                            {{-- AUTHOR --}}
                            {{-- ========================================= --}}

                            <div class="mt-4 pt-4 border-t border-gray-100">

                                <p class="text-xs text-gray-400">

                                    Created by
                                    {{ $post->author?->name ?? 'Admin' }}

                                </p>

                            </div>


                            {{-- ========================================= --}}
                            {{-- ACTION BUTTONS --}}
                            {{-- ========================================= --}}

                            <div class="mt-5 flex flex-wrap items-start gap-3">


                                {{-- ========================================= --}}
                                {{-- EDIT AREA --}}
                                {{-- ========================================= --}}

                                <details class="w-full">

                                    <summary
                                        class="inline-flex cursor-pointer list-none px-4 py-2 bg-[#849753] hover:bg-[#6F4E37] text-white text-sm font-semibold rounded-lg"
                                    >
                                        Edit Post
                                    </summary>


                                    {{-- Edit form --}}

                                    <form
                                        method="POST"
                                        action="{{ route('admin.posts.update', $post) }}"
                                        class="mt-4 p-5 bg-gray-50 border border-gray-200 rounded-xl space-y-4"
                                    >

                                        @csrf

                                        @method('PUT')


                                        {{-- Edit title --}}

                                        <div>

                                            <label
                                                for="edit_title_{{ $post->id }}"
                                                class="block text-sm font-medium text-gray-700 mb-2"
                                            >
                                                Title
                                            </label>

                                            <input
                                                type="text"
                                                name="title"
                                                id="edit_title_{{ $post->id }}"
                                                value="{{ $post->title }}"
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]"
                                            >

                                        </div>


                                        {{-- Edit content --}}

                                        <div>

                                            <label
                                                for="edit_content_{{ $post->id }}"
                                                class="block text-sm font-medium text-gray-700 mb-2"
                                            >
                                                Description
                                            </label>

                                            <textarea
                                                name="content"
                                                id="edit_content_{{ $post->id }}"
                                                rows="4"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]"
                                            >{{ $post->content }}</textarea>

                                        </div>


                                        {{-- Edit date and time --}}

                                        <div>

                                            <label
                                                for="edit_published_at_{{ $post->id }}"
                                                class="block text-sm font-medium text-gray-700 mb-2"
                                            >
                                                Date and Time
                                            </label>

                                            <input
                                                type="datetime-local"
                                                name="published_at"
                                                id="edit_published_at_{{ $post->id }}"
                                                value="{{ $post->published_at?->format('Y-m-d\TH:i') }}"
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]"
                                            >

                                        </div>


                                        {{-- Save changes --}}

                                        <button
                                            type="submit"
                                            class="px-5 py-2 bg-[#849753] hover:bg-[#6F4E37] text-white text-sm font-semibold rounded-lg transition"
                                        >
                                            Save Changes
                                        </button>

                                    </form>

                                </details>


                                {{-- ========================================= --}}
                                {{-- DELETE FORM --}}
                                {{-- ========================================= --}}

                                <form
                                    method="POST"
                                    action="{{ route('admin.posts.destroy', $post) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this post?');"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-[#6F4E37] hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition"
                                    >
                                        Delete Post
                                    </button>

                                </form>

                            </div>

                        </div>


                    @empty

                        {{-- No posts --}}

                        <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">

                            No posts have been created yet.

                        </div>

                    @endforelse

                </div>


                {{-- ========================================= --}}
                {{-- PAGINATION --}}
                {{-- ========================================= --}}

                @if($posts->hasPages())

                    <div class="mt-6">

                        {{ $posts->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>


@push('scripts')
    

    <script>

        const createPostModal = document.getElementById('createPostModal');


        // =========================================
        // OPEN MODAL
        // =========================================

        function openCreatePostModal() {

            createPostModal.classList.remove('hidden');

            // Prevent page scrolling while modal is open
            document.body.classList.add('overflow-hidden');

        }


        // =========================================
        // CLOSE MODAL
        // =========================================

        function closeCreatePostModal() {

            createPostModal.classList.add('hidden');

            // Allow page scrolling again
            document.body.classList.remove('overflow-hidden');

        }


        // =========================================
        // ESCAPE KEY
        // =========================================

        document.addEventListener('keydown', function(event) {

            if (event.key === 'Escape') {

                closeCreatePostModal();

            }

        });


        // =========================================
        // IF VALIDATION ERRORS EXIST
        // KEEP MODAL OPEN
        // =========================================

        @if($errors->any())

            document.body.classList.add('overflow-hidden');

        @endif

    </script>

@endpush
