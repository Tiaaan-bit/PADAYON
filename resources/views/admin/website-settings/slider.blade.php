@extends('layouts.admin')

@section('title', 'Home Page Slider Images')

@section('content')
    <div class="min-h-screen space-y-6 p-4 sm:p-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Home Page Slider Images
            </h1>

        </div>


        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @for ($slot = 1; $slot <= 5; $slot++)
                @php
                    $image = $images->get($slot);

                    $previewUrl = $image
                        ? asset('storage/' . $image->image_path)
                        : asset("build/assets/images/image-{$slot}.webp");
                @endphp

                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="border-b border-gray-100 p-4">
                        <h2 class="font-semibold text-gray-800">
                            Slider Image {{ $slot }}
                        </h2>

                        <p class="mt-1 text-xs text-gray-500">
                            {{ $image ? 'Custom image uploaded' : 'Using the default image' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4">
                        <img src="{{ $previewUrl }}" alt="Home page slider image {{ $slot }}"
                            class="aspect-video w-full rounded-xl object-cover">
                    </div>

                    <div class="space-y-3 p-4">
                        <form action="{{ route('admin.home-slider.update', $slot) }}" method="POST"
                            enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="image-{{ $slot }}" class="mb-2 block text-sm font-medium text-gray-700">
                                    Choose replacement image
                                </label>

                                <input id="image-{{ $slot }}" name="image" type="file"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required
                                    class="block w-full rounded-xl border border-gray-300 text-sm text-gray-700 file:mr-3 file:border-0 file:bg-gray-100 file:px-4 file:py-3 file:font-medium hover:file:bg-gray-200">
                            </div>

                            <button type="submit"
                                class="w-full rounded-xl bg-[#849753] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#718344]">
                                Upload / Replace Image
                            </button>
                        </form>

                        @if ($image)
                            <form action="{{ route('admin.home-slider.destroy', $slot) }}" method="POST"
                                onsubmit="return confirm('Remove this custom image and restore the default image?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="w-full rounded-xl border border-red-200 px-4 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                    Restore Default Image
                                </button>
                            </form>
                        @endif
                    </div>
                </section>
            @endfor
        </div>
    </div>
@endsection
