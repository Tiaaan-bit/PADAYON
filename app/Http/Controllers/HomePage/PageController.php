<?php

namespace App\Http\Controllers\HomePage;

use App\Http\Controllers\Controller;
use App\Models\AboutPageSetting;
use App\Models\AddOns;
use App\Models\HomepageSection;
use App\Models\HomeSliderImage;
use App\Models\Services;
use App\Models\Therapists;
use App\Models\WebsiteSetting;

class PageController extends Controller
{
    public function showHomePage()
    {
        $therapists = Therapists::query()
            ->whereIn('status', ['available', 'unavailable'])
            ->orderBy('name')
            ->get();

        $homeSliderImages = HomeSliderImage::all()->keyBy('slot');

        $heroSection = HomepageSection::where('section_key', 'hero')->first();

        $introSection = HomepageSection::where('section_key', 'introduction')->first();

        $whyChooseSection = HomepageSection::where('section_key', 'why_choose')->first();

        $whyChooseBoxes = HomepageSection::where('section_key', 'like', 'why_choose_box_%')->get()->keyBy('section_key');

        return view('home.home', compact('therapists', 'homeSliderImages', 'heroSection', 'introSection','whyChooseSection','whyChooseBoxes'));
    }

    public function showAboutPage()
    {
        $aboutPageSetting = AboutPageSetting::firstOrCreate(
            ['id' => 1],
            [
                'heading' => 'What we do?',
                'description' => 'Our web-based Blind Therapist Massage Appointment System is designed to connect clients with professional blind massage therapists through a simple, accessible, and convenient online platform.
    
    We aim to provide quality wellness services while creating employment opportunities and promoting the skills of visually impaired massage therapists.',
            ],
        );

        return view('home.about', compact('aboutPageSetting'));
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
