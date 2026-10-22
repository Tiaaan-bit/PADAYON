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
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function showHomePage()
    {
        // Redirect authenticated therapists to Padayon AI
        if (Auth::guard('therapist')->check()) {
            return redirect()->route('therapist.ai.index');
        }

        // Redirect authenticated web users
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isStaff()) {
                return redirect()->route('staff.dashboard');
            }

            // Require email verification for normal users
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->route('user.dashboard');
        }

        // Load existing homepage data for guests
        $therapists = Therapists::query()
            ->whereIn('status', ['available', 'unavailable'])
            ->orderBy('name')
            ->get();

        $homeSliderImages = HomeSliderImage::all()->keyBy('slot');

        $heroSection = HomepageSection::where('section_key', 'hero')->first();

        $introSection = HomepageSection::where('section_key', 'introduction')->first();

        $whyChooseSection = HomepageSection::where('section_key', 'why_choose')->first();

        $whyChooseBoxes = HomepageSection::where('section_key', 'like', 'why_choose_box_%')->get()->keyBy('section_key');

        return view('home.home', compact('therapists', 'homeSliderImages', 'heroSection', 'introSection', 'whyChooseSection', 'whyChooseBoxes'));
    }

    public function showAboutPage()
    {
        // Redirect authenticated therapists to Padayon AI
        if (Auth::guard('therapist')->check()) {
            return redirect()->route('therapist.ai.index');
        }

        // Redirect authenticated web users
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isStaff()) {
                return redirect()->route('staff.dashboard');
            }

            // Require email verification for normal users
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->route('user.dashboard');
        }

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
        // Redirect authenticated therapists to Padayon AI
        if (Auth::guard('therapist')->check()) {
            return redirect()->route('therapist.ai.index');
        }

        // Redirect authenticated web users
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isStaff()) {
                return redirect()->route('staff.dashboard');
            }

            // Require email verification for normal users
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->route('user.dashboard');
        }

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
        // Redirect authenticated therapists to Padayon AI
        if (Auth::guard('therapist')->check()) {
            return redirect()->route('therapist.ai.index');
        }

        // Redirect authenticated web users
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isStaff()) {
                return redirect()->route('staff.dashboard');
            }

            // Require email verification for normal users
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->route('user.dashboard');
        }

        $services = Services::query()->where('status', 'active')->orderBy('name')->orderBy('duration_minutes')->get()->groupBy('name');

        $addOns = AddOns::query()->where('status', 'active')->orderBy('name')->get();

        return view('home.services', compact('services', 'addOns'));
    }
}
