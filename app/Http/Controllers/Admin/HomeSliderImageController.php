<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.website-settings.slider', compact('images'));
    }

    public function update(Request $request, int $slot): RedirectResponse
    {
        abort_unless($slot >= 1 && $slot <= 5, 404);

        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $sliderImage = HomeSliderImage::where('slot', $slot)->first();

        // Store the new image first so the existing image is preserved
        // if the upload fails.
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
