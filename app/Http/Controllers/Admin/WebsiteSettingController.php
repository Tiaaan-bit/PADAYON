<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWebsiteSettingRequest;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingController extends Controller
{
    public function edit(): View
    {
        $settings = WebsiteSetting::firstOrCreate(
            ['id' => 1],
            [
                'website_name' => 'Padayon Massage Center',
                'website_subname' => 'SubName',
                'facebook_name' => 'Padayon Massage Center',
                'facebook_url' => 'https://www.facebook.com/people/Padayon-Massage-Center/61583836714033/',
                'business_address' => 'RCPJC Building Unit 1 Brgy. Salcedo 2, Noveleta, Philippines, 4105',
                'location_url' => null,
                'phone_number' => '0976 007 7035',
                'opening_time' => '13:00',
                'closing_time' => '01:00',
            ],
        );

        return view('admin.website-settings.edit', compact('settings'));
    }

    public function update(UpdateWebsiteSettingRequest $request): RedirectResponse
    {
        $settings = WebsiteSetting::firstOrCreate(
            ['id' => 1],
            [
                'website_name' => 'Padayon Massage Center',
                'website_subname' => 'SubName',
                'facebook_name' => 'Padayon Massage Center',
                'facebook_url' => 'https://www.facebook.com/people/Padayon-Massage-Center/61583836714033/',
                'business_address' => 'RCPJC Building Unit 1 Brgy. Salcedo 2, Noveleta, Philippines, 4105',
                'phone_number' => '0976 007 7035',
                'opening_time' => '13:00:00',
                'closing_time' => '01:00:00',
            ],
        );

        $validated = $request->validated();

        // The uploaded file is handled separately from the text fields.
        unset($validated['logo']);

        if ($request->hasFile('logo')) {
            $newLogoPath = $request->file('logo')->store('website', 'public');

            // Delete the old logo only after the new upload succeeds.
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $validated['logo_path'] = $newLogoPath;
        }

        $settings->update($validated);

        return redirect()->route('admin.website-settings.edit')->with('success', 'Website settings updated successfully.');
    }

    public function closeBookingsToday(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'booking_closed_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:' . Carbon::now('Asia/Manila')->toDateString()],
        ]);

        $settings = WebsiteSetting::query()->findOrFail(1);

        $settings->booking_closed_date = $validated['booking_closed_date'];
        $settings->save();

        return back()->with('success', 'Bookings have been closed for ' . Carbon::parse($validated['booking_closed_date'], 'Asia/Manila')->format('F d, Y') . '. Other dates remain available.');
    }

    public function reopenBookingsToday(): RedirectResponse
    {
        $settings = WebsiteSetting::query()->findOrFail(1);

        $settings->booking_closed_date = null;
        $settings->save();

        return back()->with('success', 'The scheduled booking closure has been removed. Bookings are open again.');
    }
}
