<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Support\PublicSiteData;
use App\Support\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(Request $request, PublicSiteData $data, SeoMeta $seo): View
    {
        $payload = $data->careersPayload($request->query('vacancy'));

        return view('public.careers', [
            ...$payload,
            'seo' => $seo->careers($payload['visibleVacancies']),
        ]);
    }
}
