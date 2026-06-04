<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Placeholder contact details (replaced by site_settings in Step 38)
    |--------------------------------------------------------------------------
    */

    'phone' => '+237 6XX XXX XXX',
    'email' => 'contact@nacho.cm',
    'address' => 'Douala, Cameroon',

    /*
    |--------------------------------------------------------------------------
    | Main navigation (order per docs/FRONTEND.md §2)
    |--------------------------------------------------------------------------
    */

    'main' => [
        ['route' => 'home', 'label' => 'navigation.home'],
        ['route' => 'about', 'label' => 'navigation.about'],
        ['route' => 'centers.index', 'label' => 'navigation.centers'],
        ['route' => 'services.index', 'label' => 'navigation.services'],
        ['route' => 'book-inspection', 'label' => 'navigation.book', 'cta' => true],
        ['route' => 'tariffs', 'label' => 'navigation.tariffs'],
        ['route' => 'inspection-process', 'label' => 'navigation.inspection_process'],
        ['route' => 'blog.index', 'label' => 'navigation.blog'],
        ['route' => 'compliance', 'label' => 'navigation.compliance'],
        ['route' => 'careers.index', 'label' => 'navigation.careers'],
        ['route' => 'contact', 'label' => 'navigation.contact'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer legal links
    |--------------------------------------------------------------------------
    */

    'legal' => [
        ['route' => 'legal.privacy', 'label' => 'footer.privacy'],
        ['route' => 'legal.terms', 'label' => 'footer.terms'],
        ['route' => 'legal.cookies', 'label' => 'footer.cookies'],
        ['route' => 'legal.notice', 'label' => 'footer.legal_notice'],
    ],

];
