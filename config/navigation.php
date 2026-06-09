<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Top utility bar (docs/DESIGN.md §3.1)
    |--------------------------------------------------------------------------
    */

    'utility_tagline_key' => 'navigation.utility_tagline',
    'opening_hours_key' => 'navigation.opening_hours_summary',

    /*
    |--------------------------------------------------------------------------
    | Main navigation (Book Inspection last — docs/FRONTEND.md §2)
    | Compliance is footer-only.
    |--------------------------------------------------------------------------
    */

    'main' => [
        ['route' => 'home', 'label' => 'navigation.home'],
        ['route' => 'about', 'label' => 'navigation.about'],
        ['route' => 'centers.index', 'label' => 'navigation.centers'],
        ['route' => 'services.index', 'label' => 'navigation.services'],
        ['route' => 'tariffs', 'label' => 'navigation.tariffs'],
        ['route' => 'inspection-process', 'label' => 'navigation.inspection_process'],
        ['route' => 'blog.index', 'label' => 'navigation.blog'],
        ['route' => 'careers.index', 'label' => 'navigation.careers'],
        ['route' => 'contact', 'label' => 'navigation.contact'],
        ['route' => 'book-inspection', 'label' => 'navigation.book', 'cta' => true],
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer quick links (includes Compliance — not in main nav)
    |--------------------------------------------------------------------------
    */

    'footer_quick_links' => [
        ['route' => 'home', 'label' => 'navigation.home'],
        ['route' => 'about', 'label' => 'navigation.about'],
        ['route' => 'centers.index', 'label' => 'navigation.centers'],
        ['route' => 'services.index', 'label' => 'navigation.services'],
        ['route' => 'tariffs', 'label' => 'navigation.tariffs'],
        ['route' => 'inspection-process', 'label' => 'navigation.inspection_process'],
        ['route' => 'blog.index', 'label' => 'navigation.blog'],
        ['route' => 'careers.index', 'label' => 'navigation.careers'],
        ['route' => 'contact', 'label' => 'navigation.contact'],
        ['route' => 'compliance', 'label' => 'navigation.compliance'],
    ],

    'footer_services' => [
        ['key' => 'periodic', 'slug' => 'periodic-inspection'],
        ['key' => 'counter', 'slug' => 'counter-visit'],
        ['key' => 'heavy', 'slug' => 'heavy-vehicles'],
        ['key' => 'pre_purchase', 'slug' => 'pre-purchase'],
        ['key' => 'road_safety', 'slug' => 'road-safety'],
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
