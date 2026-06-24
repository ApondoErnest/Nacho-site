<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Support\PublicSiteData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(Request $request, PublicSiteData $data): View
    {
        return view('public.careers', $data->careersPayload($request->query('vacancy')));
    }
}
