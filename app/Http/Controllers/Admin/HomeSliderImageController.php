<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\HomeSliderImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeSliderImageController extends Controller
{
    public function index(): View
    {
        $images = HomeSliderImage::all()->keyBy('slot');

        $heroSection = HomepageSection::firstOrCreate(
            ['section_key' => 'hero'],
            [
                'heading' => 'Meet the hands that heal and restore your body and mind.',
                'description' => "Ang pahinga mo, ang aming misyon.\nSerbisyong may puso, dignidad, at malasakit\nhatid ng aming mahuhusay na blind massage\ntherapists.\n\nSa bawat pahinga mo, nakakatulong ka rin sa\naming mga blind massage therapists na magkaroon\nng dignified livelihood.",
            ],
        );

        $introSection = HomepageSection::firstOrCreate(
            ['section_key' => 'introduction'],
            [
                'heading' => 'Looking for a place to relax, recharge, and restore your energy?',
                'description' => "Dito sa Padayon Massage Center, bawat haplos ay\nmay malasakit, bawat therapy ay may puso.\n\nOur visually impaired therapists are highly\nskilled—gifted with heightened touch and genuine\ncompassion.",
                'button_text' => 'Get Started',
            ],
        );

        // Why Choose section heading
        $whyChooseSection = HomepageSection::firstOrCreate(
            ['section_key' => 'why_choose'],
            [
                'heading' => 'Why Choose Padayon?',
                'description' => null,
                'button_text' => null,
            ],
        );

        // Five benefit boxes
        $whyChooseBoxes = collect([
            1 => 'Authentic Filipino healing touch',
            2 => 'Skilled Blind Massage Specialists',
            3 => 'Stress & anxiety relief',
            4 => 'Comforting and quiet space',
            5 => 'Helps support sustainable livelihood for the visually impaired',
        ])->map(function ($heading, $number) {
            return HomepageSection::firstOrCreate(
                ['section_key' => 'why_choose_box_' . $number],
                [
                    'heading' => $heading,
                    'description' => null,
                    'button_text' => null,
                ],
            );
        });

        return view('admin.website-settings.slider', compact('images', 'heroSection', 'introSection', 'whyChooseSection', 'whyChooseBoxes'));
    }

    /**
     * Update a homepage text section.
     */

    public function updateSection(Request $request, string $sectionKey): RedirectResponse
    {
        $allowedSections = ['hero', 'introduction', 'why_choose', 'why_choose_box_1', 'why_choose_box_2', 'why_choose_box_3', 'why_choose_box_4', 'why_choose_box_5'];

        abort_unless(in_array($sectionKey, $allowedSections, true), 404);

        /*
    |--------------------------------------------------------------------------
    | Why Choose Padayon Section
    |--------------------------------------------------------------------------
    | Save the section heading and all five benefit headings together.
    */
        if ($sectionKey === 'why_choose') {
            $validated = $request->validate([
                'heading' => ['required', 'string', 'max:1000'],
                'benefit_headings' => ['required', 'array', 'size:5'],
                'benefit_headings.1' => ['required', 'string', 'max:1000'],
                'benefit_headings.2' => ['required', 'string', 'max:1000'],
                'benefit_headings.3' => ['required', 'string', 'max:1000'],
                'benefit_headings.4' => ['required', 'string', 'max:1000'],
                'benefit_headings.5' => ['required', 'string', 'max:1000'],
            ]);

            HomepageSection::updateOrCreate(
                ['section_key' => 'why_choose'],
                [
                    'heading' => $validated['heading'],
                    'description' => null,
                    'button_text' => null,
                ],
            );

            foreach ($validated['benefit_headings'] as $number => $heading) {
                HomepageSection::updateOrCreate(
                    ['section_key' => 'why_choose_box_' . $number],
                    [
                        'heading' => $heading,
                        'description' => null,
                        'button_text' => null,
                    ],
                );
            }

            return redirect()->route('admin.home-slider.index')->with('success', 'Why Choose Padayon section updated successfully.');
        }

        /*
    |--------------------------------------------------------------------------
    | Hero and Introduction Sections
    |--------------------------------------------------------------------------
    */
        $rules = [
            'heading' => ['required', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:10000'],
            'button_text' => ['nullable', 'string', 'max:100'],
        ];

        if (in_array($sectionKey, ['hero', 'introduction'], true)) {
            $rules['description'] = ['required', 'string', 'max:10000'];
        }

        if ($sectionKey === 'introduction') {
            $rules['button_text'] = ['required', 'string', 'max:100'];
        }

        $validated = $request->validate($rules);

        HomepageSection::updateOrCreate(
            ['section_key' => $sectionKey],
            [
                'heading' => $validated['heading'],
                'description' => $validated['description'] ?? null,
                'button_text' => $validated['button_text'] ?? null,
            ],
        );

        return redirect()->route('admin.home-slider.index')->with('success', 'Homepage text updated successfully.');
    }

    /**
     * Upload or replace a slider image.
     */
    public function update(Request $request, int $slot): RedirectResponse
    {
        abort_unless($slot >= 1 && $slot <= 5, 404);

        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $sliderImage = HomeSliderImage::where('slot', $slot)->first();

        // Store the new image before deleting the old one.
        $newPath = $validated['image']->store('home-slider', 'public');

        if (!$newPath) {
            return back()->withErrors([
                'image' => 'Unable to upload the image. Please try again.',
            ]);
        }

        if ($sliderImage) {
            $oldPath = $sliderImage->image_path;

            $sliderImage->update([
                'image_path' => $newPath,
            ]);

            if ($oldPath !== $newPath) {
                Storage::disk('public')->delete($oldPath);
            }
        } else {
            HomeSliderImage::create([
                'slot' => $slot,
                'image_path' => $newPath,
            ]);
        }

        return back()->with('success', "Slider image {$slot} updated successfully.");
    }

    /**
     * Restore a slider image to its default.
     */
    public function destroy(int $slot): RedirectResponse
    {
        abort_unless($slot >= 1 && $slot <= 5, 404);

        $sliderImage = HomeSliderImage::where('slot', $slot)->first();

        if ($sliderImage) {
            $path = $sliderImage->image_path;

            $sliderImage->delete();

            Storage::disk('public')->delete($path);
        }

        return back()->with('success', "Slider image {$slot} reset to its default image.");
    }
}
