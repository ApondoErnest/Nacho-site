<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Support\PublicSiteData;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function privacy(PublicSiteData $data): View
    {
        return $this->legal('privacy-policy', 'footer.privacy', $data);
    }

    public function terms(PublicSiteData $data): View
    {
        return $this->legal('terms-and-conditions', 'footer.terms', $data);
    }

    public function cookies(PublicSiteData $data): View
    {
        return $this->legal('cookie-policy', 'footer.cookies', $data);
    }

    public function notice(PublicSiteData $data): View
    {
        return $this->legal('legal-notice', 'footer.legal_notice', $data);
    }

    private function legal(string $slug, string $titleKey, PublicSiteData $data): View
    {
        return view('public.placeholder', [
            'pageTitle' => $titleKey,
            'page' => $data->legalPage($slug),
        ]);
    }
}
