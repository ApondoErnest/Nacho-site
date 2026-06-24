<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    public function blog(): View
    {
        return view('public.placeholder', ['pageTitle' => 'navigation.blog']);
    }

    public function compliance(): View
    {
        return view('public.placeholder', ['pageTitle' => 'navigation.compliance']);
    }
}
