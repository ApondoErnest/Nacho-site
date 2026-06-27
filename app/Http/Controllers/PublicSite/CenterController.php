<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Support\PublicSiteData;
use App\Support\SeoMeta;
use Illuminate\View\View;

class CenterController extends Controller
{
    public function index(PublicSiteData $data, SeoMeta $seo): View
    {
        $centers = $data->centers();

        return view('public.centers-index', [
            'centerRecords' => $centers,
            'serviceItems' => $data->services(),
            'seo' => $seo->centers($centers),
        ]);
    }
}
