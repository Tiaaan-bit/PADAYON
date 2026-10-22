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

                <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">

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

    {{-- HOMEPAGE TEXT SECTIONS --}}
    <div class="min-h-screen space-y-6 p-4 sm:p-6">

        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Home Page Content
            </h2>
        </div>
        <div class="grid grid-cols-1 items-stretch gap-6 xl:grid-cols-3">
            {{-- INTRODUCTION SECTION --}}
            <section
                class="flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-5">
                    <h3 class="text-lg font-bold text-gray-800">
                        Introduction Section
                    </h3>
                </div>

                <form action="{{ route('admin.home-slider.section.update', 'introduction') }}" method="POST"
                    class="flex flex-1 flex-col space-y-5 p-5">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="section_key" value="introduction">

                    <div>
                        <label for="intro_heading" class="mb-2 block text-sm font-semibold text-gray-700">
                            Heading
                        </label>

                        <textarea id="intro_heading" name="heading" rows="3" required maxlength="1000"
                            class="w-full rounded-xl border border-gray-300 p-3 focus:border-[#849753] focus:ring-[#849753]">{{ old('section_key') === 'introduction' ? old('heading') : $introSection->heading }}</textarea>

                        @if (old('section_key') === 'introduction')
                            @error('heading')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="intro_description" class="mb-2 block text-sm font-semibold text-gray-700">
                            Description
                        </label>

                        <textarea id="intro_description" name="description" rows="7" required maxlength="10000"
                            class="w-full rounded-xl border border-gray-300 p-3 focus:border-[#849753] focus:ring-[#849753]">{{ old('section_key') === 'introduction' ? old('description') : $introSection->description }}</textarea>

                        @if (old('section_key') === 'introduction')
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="intro_button_text" class="mb-2 block text-sm font-semibold text-gray-700">
                            Button Text
                        </label>

                        <input id="intro_button_text" type="text" name="button_text"
                            value="{{ old('section_key') === 'introduction' ? old('button_text') : $introSection->button_text }}"
                            required maxlength="100"
                            class="w-full rounded-xl border border-gray-300 p-3 focus:border-[#849753] focus:ring-[#849753]">

                        @if (old('section_key') === 'introduction')
                            @error('button_text')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div class="mt-auto flex justify-end"> <button type="submit"
                            class="rounded-xl bg-[#849753] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#718344]">
                            Save
                        </button>
                    </div>
                </form>
            </section>

            {{-- HERO SECTION --}}
            <section
                class="flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-5">
                    <h3 class="text-lg font-bold text-gray-800">
                        Hero Section
                    </h3>
                </div>

                <form action="{{ route('admin.home-slider.section.update', 'hero') }}" method="POST"
                    class="flex flex-1 flex-col space-y-5 p-5">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="section_key" value="hero">

                    <div>
                        <label for="hero_heading" class="mb-2 block text-sm font-semibold text-gray-700">
                            Heading
                        </label>

                        <textarea id="hero_heading" name="heading" rows="3" required maxlength="1000"
                            class="w-full rounded-xl border border-gray-300 p-3 focus:border-[#849753] focus:ring-[#849753]">{{ old('section_key') === 'hero' ? old('heading') : $heroSection->heading }}</textarea>

                        @if (old('section_key') === 'hero')
                            @error('heading')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="hero_description" class="mb-2 block text-sm font-semibold text-gray-700">
                            Description
                        </label>

                        <textarea id="hero_description" name="description" rows="8" required maxlength="10000"
                            class="w-full rounded-xl border border-gray-300 p-3 focus:border-[#849753] focus:ring-[#849753]">{{ old('section_key') === 'hero' ? old('description') : $heroSection->description }}</textarea>

                        @if (old('section_key') === 'hero')
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div class="mt-auto flex justify-end"> <button type="submit"
                            class="rounded-xl bg-[#849753] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#718344]">
                            Save
                        </button>
                    </div>
                </form>
            </section>


            {{-- WHY CHOOSE PADAYON SECTION --}}
            <section
                class="flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-5">
                    <h3 class="text-lg font-bold text-gray-800">
                        Why Choose Padayon Section
                    </h3>
                </div>

                <form action="{{ route('admin.home-slider.section.update', 'why_choose') }}" method="POST"
                    class="flex flex-1 flex-col space-y-5 p-5">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="section_key" value="why_choose">

                    {{-- MAIN SECTION HEADING --}}
                    <div>
                        <label for="why_choose_heading" class="mb-2 block text-sm font-semibold text-gray-700">
                            Section Heading
                        </label>

                        <input id="why_choose_heading" type="text" name="heading"
                            value="{{ old('section_key') === 'why_choose' ? old('heading') : $whyChooseSection->heading }}"
                            required maxlength="1000"
                            class="w-full rounded-xl border border-gray-300 p-3 focus:border-[#849753] focus:ring-[#849753]">

                        @if (old('section_key') === 'why_choose')
                            @error('heading')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    {{-- FIVE BENEFIT BOX HEADINGS --}}
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        @foreach ($whyChooseBoxes as $number => $box)
                            <div>
                                <label for="benefit_heading_{{ $number }}"
                                    class="mb-2 block text-sm font-semibold text-gray-700">
                                    Benefit Box {{ $number }}
                                </label>

                                <textarea id="benefit_heading_{{ $number }}" name="benefit_headings[{{ $number }}]" rows="3"
                                    required maxlength="1000"
                                    class="w-full rounded-xl border border-gray-300 p-3 focus:border-[#849753] focus:ring-[#849753]">{{ old('section_key') === 'why_choose' ? old('benefit_headings.' . $number, $box->heading) : $box->heading }}</textarea>

                                @if (old('section_key') === 'why_choose')
                                    @error("benefit_headings.{$number}")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                @endif
                            </div>
                        @endforeach

                    </div>

                    {{-- SAVE ALL --}}
                    <div class="mt-auto flex justify-end"> <button type="submit"
                            class="rounded-xl bg-[#849753] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#718344]">
                            Save
                        </button>
                    </div>

                </form>
            </section>



        </div>

    </div>

@endsection
