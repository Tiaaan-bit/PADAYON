<?php

namespace App\Http\Controllers\HomePage;

use App\Http\Controllers\Controller;
use App\Models\AddOns;
use App\Models\Services;
use App\Models\Therapists;
use App\Models\WebsiteSetting;
use App\Models\HomeSliderImage;


class PageController extends Controller
{
    public function showHomePage()
    {
        $therapists = Therapists::query()
            ->whereIn('status', ['available', 'unavailable'])
            ->orderBy('name')
            ->get();

        $homeSliderImages = HomeSliderImage::all()->keyBy('slot');

        return view('home.home', compact('therapists','homeSliderImages'));
    }

    public function showAboutPage()
    {
        return view('home.about');
    }

    public function showContactPage()
    {
        $settings = WebsiteSetting::firstOrCreate(
            ['id' => 1],
            [
                'facebook_name' => 'Padayon Massage Center',
                'facebook_url' => 'https://www.facebook.com/people/Padayon-Massage-Center/61583836714033/',
                'business_address' => 'RCPJC Building Unit 1 Brgy. Salcedo 2, Noveleta, Philippines, 4105',
                'location_url' => null,
                'phone_number' => '0976 007 7035',
                'opening_time' => '13:00',
                'closing_time' => '01:00',
            ],
        );

        return view('home.contacts', compact('settings'));
    }

    public function showServicesPage()
    {
        $services = Services::query()->where('status', 'active')->orderBy('name')->orderBy('duration_minutes')->get()->groupBy('name');

        $addOns = AddOns::query()->where('status', 'active')->orderBy('name')->get();

        return view('home.services', compact('services', 'addOns'));
    }
}
