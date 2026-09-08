<?php

namespace App\Http\Controllers\HomePage;

use App\Http\Controllers\Controller;

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
        return view('home.services');
    }
}
