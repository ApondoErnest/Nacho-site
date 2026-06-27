<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Support\PublicSiteData;
use App\Support\SeoMeta;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(PublicSiteData $data, SeoMeta $seo): View
    {
        return view('welcome', [
            'seo' => $seo->home($data->headquarters()),
        ]);
    }
}
