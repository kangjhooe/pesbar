<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StaticPageController extends Controller
{
    /**
     * Display the Terms and Conditions page.
     */
    public function terms(): View
    {
        return view('static.terms');
    }

    /**
     * Display the Privacy Policy page.
     */
    public function privacy(): View
    {
        return view('static.privacy');
    }
}

