<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Support\PublicSiteData;
use Illuminate\View\View;

class TariffController extends Controller
{
    public function index(PublicSiteData $data): View
    {
        return view('public.tariffs', [
            'tariffPreviewRows' => $data->tariffPreview(),
        ]);
    }
}
