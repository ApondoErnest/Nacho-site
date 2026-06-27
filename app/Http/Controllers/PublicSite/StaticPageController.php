<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Support\PublicSiteData;
use App\Support\SeoMeta;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function privacy(PublicSiteData $data, SeoMeta $seo): View
    {
        return $this->legal('privacy-policy', 'footer.privacy', 'privacy', $data, $seo);
    }

    public function terms(PublicSiteData $data, SeoMeta $seo): View
    {
        return $this->legal('terms-and-conditions', 'footer.terms', 'terms', $data, $seo);
    }

    public function cookies(PublicSiteData $data, SeoMeta $seo): View
    {
        return $this->legal('cookie-policy', 'footer.cookies', 'cookies', $data, $seo);
    }

    public function notice(PublicSiteData $data, SeoMeta $seo): View
    {
        return $this->legal('legal-notice', 'footer.legal_notice', 'legal_notice', $data, $seo);
    }

    private function legal(string $slug, string $titleKey, string $seoPage, PublicSiteData $data, SeoMeta $seo): View
    {
        $page = $data->legalPage($slug);

        return view('public.placeholder', [
            'pageTitle' => $titleKey,
            'page' => $page,
            'seo' => $page ? $seo->legalPage($page, $seoPage) : $seo->page($seoPage),
        ]);
    }
}
