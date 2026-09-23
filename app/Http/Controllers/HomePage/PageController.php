<?php

namespace App\Http\Controllers\HomePage;

use App\Http\Controllers\Controller;
use App\Models\AddOns;
use App\Models\Services;

class PageController extends Controller
{
    public function showHomePage()
    {
        return view('home.home');
    }

    public function showAboutPage()
    {
        return view('home.about');
    }

    public function showContactPage()
    {
        return view('home.contacts');
    }

    public function showServicesPage()
    {
        $services = Services::query()->where('status', 'active')->orderBy('name')->orderBy('duration_minutes')->get()->groupBy('name');

        $addOns = AddOns::query()->where('status', 'active')->orderBy('name')->get();

        return view('home.services', compact('services', 'addOns'));
    }
}
