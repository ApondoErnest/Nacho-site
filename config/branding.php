<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brand assets
    |--------------------------------------------------------------------------
    |
    | Path relative to /public. Overridable via site_settings.logo in Step 38.
    |
    */

    'logo' => 'images/nacho-logo.png',

    'logo_alt' => 'branding.logo_alt',

    /*
    | Logo display contexts (see <x-nacho-logo context="…">)
    | nav: compact header bar | footer: brand column | auth: login/register
    */

    'logo_contexts' => [
        'nav' => 'Navigation bar (compact)',
        'footer' => 'Site footer',
        'auth' => 'Authentication pages',
    ],

    'favicon' => 'images/nacho-logo.png',

    /*
    | Homepage / marketing imagery (replace SVG placeholders with real photos).
    */

    'home_hero_image' => 'images/hero-inspection.svg',

];
