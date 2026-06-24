<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Support\PublicSiteData;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(PublicSiteData $data): View
    {
        return view('public.services-index', [
            'serviceItems' => $data->services(),
        ]);
    }
}
