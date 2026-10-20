@extends('layouts.admin')

@section('title', 'About Page Settings')

@section('content')
<div class="p-6">

    <div class="max-w-4xl mx-auto">

        <h1 class="text-2xl font-bold text-[#2F2420] mb-6">
            About Page Settings
        </h1>

        <form
            action="{{ route('admin.about-settings.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 rounded-2xl bg-white p-6 shadow"
        >
            @csrf
            @method('PUT')

            <div>
                <label for="heading"
                       class="mb-2 block font-semibold text-gray-700">
                    About Heading
                </label>

                <input
                    type="text"
                    id="heading"
                    name="heading"
                    value="{{ old('heading', $aboutPageSetting->heading) }}"
                    required
                    class="w-full rounded-lg border border-gray-300 p-3
                           focus:border-[#849753] focus:outline-none"
                >
            </div>

            <div>
                <label for="description"
                       class="mb-2 block font-semibold text-gray-700">
                    About Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="8"
                    required
                    class="w-full rounded-lg border border-gray-300 p-3
                           focus:border-[#849753] focus:outline-none"
                >{{ old('description', $aboutPageSetting->description) }}</textarea>
            </div>

            <div>
                <label class="mb-2 block font-semibold text-gray-700">
                    Current About Image
                </label>

                @if ($aboutPageSetting->image)
                    <img
                        src="{{ asset('storage/' . $aboutPageSetting->image) }}"
                        alt="Current About image"
                        class="mb-4 h-64 w-full max-w-lg rounded-xl object-cover"
                    >
                @else
                    <p class="mb-4 text-sm text-gray-500">
                        No image uploaded yet.
                    </p>
                @endif

                <label for="image"
                       class="mb-2 block font-semibold text-gray-700">
                    Upload Replacement Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full rounded-lg border border-gray-300 p-3"
                >

                <p class="mt-2 text-sm text-gray-500">
                    JPG, PNG, or WebP. Maximum file size: 2 MB.
                    Leave empty to keep the current image.
                </p>
            </div>

            <button
                type="submit"
                class="rounded-lg bg-[#849753] px-6 py-3
                       font-semibold text-white transition
                       hover:bg-[#6f8142]"
            >
                Save Changes
            </button>

        </form>

    </div>

</div>
@endsection
