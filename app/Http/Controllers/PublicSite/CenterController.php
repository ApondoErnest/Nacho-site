<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Support\PublicSiteData;
use Illuminate\View\View;

class CenterController extends Controller
{
    public function index(PublicSiteData $data): View
    {
        return view('public.centers-index', [
            'centerRecords' => $data->centers(),
            'serviceItems' => $data->services(),
        ]);
    }
}
