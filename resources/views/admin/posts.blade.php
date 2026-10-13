@extends('layouts.admin')

@section('title', 'Padayon Massage Center - Announcement')

@section('content')

    <div class="min-h-screen flex flex-col">

        <div class="flex-1 p-4 sm:p-6">

            <div class="max-w-5xl mx-auto">

                {{-- ========================================= --}}
                {{-- PAGE HEADER --}}
                {{-- ========================================= --}}

                <div class="mb-8">

                    <h1 class="text-3xl font-bold text-gray-800">
                        Announcements
                    </h1>
                </div>




                {{-- CREATE TEXT OR IMAGE POST --}}
                <div class="mb-8 flex flex-wrap gap-3">
                    <button type="button" onclick="openCreatePostModal()"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#849753]
               px-5 py-3 font-semibold text-white transition
               hover:bg-[#6F4E37]">
                        <span class="text-xl">+</span>
                        Create Text Announcement
                    </button>

                    <button type="button" onclick="openImagePostModal()"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#6F4E37]
               px-5 py-3 font-semibold text-white transition
               hover:bg-[#849753]">
                        <span class="text-xl">+</span>
                        Post an Image
                    </button>
                </div>


                {{-- ========================================= --}}
                {{-- CREATE POST MODAL --}}
                {{-- ========================================= --}}

                <div id="createPostModal"
                    class="{{ $errors->has('title') || $errors->has('content') || $errors->has('published_at') ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto"
                    aria-labelledby="create-post-title" role="dialog" aria-modal="true">

                    {{-- ========================================= --}}
                    {{-- DARK BACKGROUND OVERLAY --}}
                    {{-- ========================================= --}}

                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCreatePostModal()"></div>


                    {{-- ========================================= --}}
                    {{-- MODAL CONTAINER --}}
                    {{-- ========================================= --}}

                    <div class="relative min-h-screen flex items-center justify-center p-4">

                        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto"
                            onclick="event.stopPropagation()">

                            {{-- ========================================= --}}
                            {{-- MODAL HEADER --}}
                            {{-- ========================================= --}}

                            <div class="flex items-start justify-between p-6 border-b border-gray-100">

                                <div>

                                    <h2 id="create-post-title" class="text-xl font-bold text-gray-800">
                                        Create New Post
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Add a title, description, and date and time for the promotion.
                                    </p>

                                </div>


                                {{-- Close X --}}

                                <button type="button" onclick="closeCreatePostModal()"
                                    class="ml-4 text-gray-400 hover:text-gray-700 text-3xl font-bold leading-none transition"
                                    aria-label="Close">
                                    &times;
                                </button>

                            </div>


                            {{-- ========================================= --}}
                            {{-- CREATE POST FORM --}}
                            {{-- ========================================= --}}

                            <form method="POST" action="{{ route('admin.posts.store') }}" class="p-6 space-y-6">

                                @csrf


                                {{-- ========================================= --}}
                                {{-- TITLE --}}
                                {{-- ========================================= --}}

                                <div>

                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        Title
                                    </label>

                                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                                        placeholder="Example: 20% Off Our Bed Massage Services" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]">

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

                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>

                                    <textarea name="content" id="content" rows="5" placeholder="Describe the service promotion..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]">{{ old('content') }}</textarea>

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

                                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Date and Time
                                    </label>

                                    <input type="datetime-local" name="published_at" id="published_at"
                                        value="{{ old('published_at') }}" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]">

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

                                    <button type="submit"
                                        class="flex-1 py-3 bg-[#849753] hover:bg-[#6F4E37] active:scale-[.99] text-white font-semibold rounded-lg transition-all">
                                        Publish Post
                                    </button>


                                    {{-- Cancel --}}

                                    <button type="button" onclick="closeCreatePostModal()"
                                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                                        Cancel
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

                {{-- ========================================= --}}
                {{-- IMAGE POST MODAL --}}
                {{-- ========================================= --}}
                <div id="imagePostModal"
                    class="{{ $errors->imagePost->any() ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto"
                    role="dialog" aria-modal="true" aria-labelledby="image-post-title">
                    {{-- OVERLAY --}}
                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeImagePostModal()"></div>

                    {{-- MODAL CONTAINER --}}
                    <div class="relative flex min-h-screen items-center justify-center p-4">
                        <div class="relative max-h-[90vh] w-full max-w-xl overflow-y-auto
               rounded-2xl bg-white shadow-2xl"
                            onclick="event.stopPropagation()">
                            {{-- HEADER --}}
                            <div class="flex items-start justify-between border-b border-gray-100 p-6">
                                <div>
                                    <h2 id="image-post-title" class="text-xl font-bold text-gray-800">
                                        Publish an Image
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Share an image with customers and therapists.
                                    </p>
                                </div>

                                <button type="button" onclick="closeImagePostModal()"
                                    class="ml-4 text-3xl leading-none text-gray-400
                       transition hover:text-gray-700"
                                    aria-label="Close">
                                    &times;
                                </button>
                            </div>

                            {{-- UPLOAD FORM --}}
                            <form method="POST" action="{{ route('admin.image-posts.store') }}"
                                enctype="multipart/form-data" class="space-y-5 p-6">
                                @csrf

                                <div>
                                    <label for="image" class="mb-2 block text-sm font-medium text-gray-700">
                                        Select Image
                                    </label>

                                    <input type="file" name="image" id="image"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required
                                        class="block w-full rounded-lg border border-gray-300 p-3 text-sm">

                                    <p class="mt-2 text-xs text-gray-500">
                                        JPG, JPEG, PNG, or WebP. Maximum size: 5 MB.
                                    </p>

                                    @error('image', 'imagePost')
                                        <p class="mt-2 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- IMAGE PREVIEW --}}
                                <div id="imagePreviewContainer" class="hidden">
                                    <p class="mb-2 text-sm font-medium text-gray-700">
                                        Image Preview
                                    </p>

                                    <img id="imagePreview" src="" alt="Selected image preview"
                                        class="max-h-72 w-full rounded-lg border border-gray-200 object-contain">
                                </div>

                                {{-- BUTTONS --}}
                                <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                                    <button type="submit"
                                        class="flex-1 rounded-lg bg-[#849753] py-3
                           font-semibold text-white transition
                           hover:bg-[#6F4E37]">
                                        Publish Image
                                    </button>

                                    <button type="button" onclick="closeImagePostModal()"
                                        class="rounded-lg bg-gray-200 px-6 py-3
                           font-semibold text-gray-700 transition
                           hover:bg-gray-300">
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

                                    @if ($post->content)
                                        <p class="text-gray-600 mt-2 whitespace-pre-line wrap-break-word">
                                            {{ $post->content }}
                                        </p>
                                    @endif

                                </div>


                                {{-- Published date --}}

                                @if ($post->published_at)
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
                                        class="inline-flex cursor-pointer list-none px-4 py-2 bg-[#849753] hover:bg-[#6F4E37] text-white text-sm font-semibold rounded-lg">
                                        Edit Post
                                    </summary>


                                    {{-- Edit form --}}

                                    <form method="POST" action="{{ route('admin.posts.update', $post) }}"
                                        class="mt-4 p-5 bg-gray-50 border border-gray-200 rounded-xl space-y-4">

                                        @csrf

                                        @method('PUT')


                                        {{-- Edit title --}}

                                        <div>

                                            <label for="edit_title_{{ $post->id }}"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Title
                                            </label>

                                            <input type="text" name="title" id="edit_title_{{ $post->id }}"
                                                value="{{ $post->title }}" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]">

                                        </div>


                                        {{-- Edit content --}}

                                        <div>

                                            <label for="edit_content_{{ $post->id }}"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Description
                                            </label>

                                            <textarea name="content" id="edit_content_{{ $post->id }}" rows="4"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]">{{ $post->content }}</textarea>

                                        </div>


                                        {{-- Edit date and time --}}

                                        <div>

                                            <label for="edit_published_at_{{ $post->id }}"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Date and Time
                                            </label>

                                            <input type="datetime-local" name="published_at"
                                                id="edit_published_at_{{ $post->id }}"
                                                value="{{ $post->published_at?->format('Y-m-d\TH:i') }}" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#849753] focus:border-[#849753]">

                                        </div>


                                        {{-- Save changes --}}

                                        <button type="submit"
                                            class="px-5 py-2 bg-[#849753] hover:bg-[#6F4E37] text-white text-sm font-semibold rounded-lg transition">
                                            Save Changes
                                        </button>

                                    </form>

                                </details>


                                {{-- ========================================= --}}
                                {{-- DELETE FORM --}}
                                {{-- ========================================= --}}

                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this post?');">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit"
                                        class="px-4 py-2 bg-[#6F4E37] hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
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
                {{-- EXISTING IMAGE POSTS --}}
                {{-- ========================================= --}}
                <div class="mt-12">
                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">
                            Existing Image Posts
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($imagePosts as $imagePost)
                            <article class="overflow-hidden rounded-xl bg-white shadow">
                                <img src="{{ asset('storage/' . $imagePost->image_path) }}"
                                    alt="Padayon Massage Center image post" class="h-64 w-full bg-gray-50 object-contain"
                                    loading="lazy">

                                <div class="p-4">
                                    <p class="mb-4 text-xs text-gray-500">
                                        Published
                                        {{ $imagePost->published_at?->format('M d, Y h:i A') }}
                                    </p>

                                    <p class="mb-4 text-xs text-gray-400">
                                        Created by {{ $imagePost->author?->name ?? 'Admin' }}
                                    </p>

                                    {{-- REPLACE IMAGE --}}
                                    <details class="mb-3">
                                        <summary
                                            class="cursor-pointer rounded-lg
                                        bg-[#849753] px-4 py-2 text-center
                                        text-sm font-semibold text-white">
                                            Replace Image
                                        </summary>

                                        <form method="POST" action="{{ route('admin.image-posts.update', $imagePost) }}"
                                            enctype="multipart/form-data" class="mt-3 space-y-3">
                                            @csrf
                                            @method('PUT')

                                            <label for="replace_image_{{ $imagePost->id }}"
                                                class="block text-sm font-medium text-gray-700">
                                                Choose replacement
                                            </label>

                                            <input type="file" name="image" id="replace_image_{{ $imagePost->id }}"
                                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required
                                                class="w-full rounded-lg border border-gray-300 p-2 text-sm">

                                            <button type="submit"
                                                class="w-full rounded-lg bg-[#849753] px-4 py-2
                                       text-sm font-semibold text-white hover:bg-[#6F4E37]">
                                                Save New Image
                                            </button>
                                        </form>
                                    </details>

                                    {{-- DELETE IMAGE --}}
                                    <form method="POST" action="{{ route('admin.image-posts.destroy', $imagePost) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this image post?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="w-full rounded-lg bg-[#6F4E37] px-4 py-2
                                   text-sm font-semibold text-white hover:bg-red-700">
                                            Delete Image
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div
                                class="col-span-full rounded-xl bg-white p-8
                        text-center text-gray-500 shadow">
                                No image posts have been published yet.
                            </div>
                        @endforelse
                    </div>
                </div>


                {{-- ========================================= --}}
                {{-- PAGINATION --}}
                {{-- ========================================= --}}

                @if ($posts->hasPages())
                    <div class="mt-6">

                        {{ $posts->links() }}

                    </div>
                @endif

            </div>

        </div>

    </div>
@endsection


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

        @if ($errors->any())

            document.body.classList.add('overflow-hidden');
        @endif


        const imagePostModal = document.getElementById('imagePostModal');

        function openImagePostModal() {
            imagePostModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeImagePostModal() {
            imagePostModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Preview the selected image before uploading.
        const imageInput = document.getElementById('image');

        imageInput?.addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');

            if (!file) {
                preview.removeAttribute('src');
                container.classList.add('hidden');
                return;
            }

            if (preview.dataset.objectUrl) {
                URL.revokeObjectURL(preview.dataset.objectUrl);
            }

            const objectUrl = URL.createObjectURL(file);

            preview.src = objectUrl;
            preview.dataset.objectUrl = objectUrl;
            container.classList.remove('hidden');
        });

        // Close both modals using Escape.
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeCreatePostModal();
                closeImagePostModal();
            }
        });

        // Keep the image modal open after image validation errors.
        @if ($errors->imagePost->any())
            document.body.classList.add('overflow-hidden');
        @endif
    </script>
@endpush
