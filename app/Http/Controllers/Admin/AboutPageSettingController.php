<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutPageSettingController extends Controller
{
    public function edit()
    {
        $aboutPageSetting = AboutPageSetting::firstOrCreate(
            ['id' => 1],
            [
                'heading' => 'What we do?',
                'description' => 'Our web-based Blind Therapist Massage Appointment System is designed to connect clients with professional blind massage therapists through a simple, accessible, and convenient online platform.

We aim to provide quality wellness services while creating employment opportunities and promoting the skills of visually impaired massage therapists.',
            ],
        );

        return view('admin.website-settings.about', compact('aboutPageSetting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'heading' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $aboutPageSetting = AboutPageSetting::firstOrCreate(
            ['id' => 1],
            [
                'heading' => 'What we do?',
                'description' => '',
            ],
        );

        $oldImagePath = $aboutPageSetting->image;
        $newImagePath = null;

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('about', 'public');
        }

        try {
            $aboutPageSetting->heading = $validated['heading'];
            $aboutPageSetting->description = $validated['description'];

            if ($newImagePath) {
                $aboutPageSetting->image = $newImagePath;
            }

            $aboutPageSetting->save();
        } catch (\Throwable $e) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $e;
        }

        if ($newImagePath && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('admin.about-settings.edit')->with('success', 'About page updated successfully.');
    }
}
