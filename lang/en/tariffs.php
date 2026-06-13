<?php

return [
    'meta' => [
        'title' => 'Inspection Tariffs',
    ],

    'hero' => [
        'eyebrow' => 'Inspection Tariffs',
        'title' => 'Transparent Vehicle Inspection Tariffs',
        'subtitle' => 'Find the applicable inspection fee and validity period for your vehicle category before visiting a NACHO center.',
        'proof_label' => 'Tariff highlights',
        'proof' => [
            'categories' => 'Clear vehicle categories',
            'published' => 'Published inspection fees',
            'no_hidden' => 'No hidden information',
        ],
        'notice' => [
            'title' => 'Official tariff notice',
            'text' => 'The tariffs displayed follow the applicable vehicle inspection schedule and may be revised following official regulatory decisions.',
        ],
        'meta' => [
            'last_verified_label' => 'Last verified:',
            'last_verified' => 'May 20, 2024',
            'effective_label' => 'Effective from:',
            'effective' => 'June 1, 2022',
            'notice_link' => 'View tariff notice',
        ],
    ],

    'console' => [
        'step' => '1. Find your tariff',
        'title' => 'Find the Tariff for Your Vehicle',
        'subtitle' => 'Select the vehicle type that best describes your vehicle.',
        'category_label' => 'Vehicle tariff categories',
        'selected_label' => 'Selected category',
        'mobile_select_label' => 'Vehicle category',
        'fee_label' => 'Inspection fee',
        'validity_label' => 'Validity period',
        'book_category' => 'Book This Category',
        'documents' => 'View Required Documents',
        'show_all' => 'Show All Tariffs',
        'help' => [
            'text' => 'Not sure which category applies to your vehicle?',
            'link' => 'Ask a NACHO Center',
        ],
        'rows' => [
            'row_1' => [
                'code' => 'A',
                'card_title' => 'Taxi / Driving School',
                'panel_title' => 'Taxi / Driving School',
                'description' => 'For taxis, driving school cars, and vehicles requiring frequent public-service inspection.',
            ],
            'row_2' => [
                'code' => 'B',
                'card_title' => 'Private Passenger Vehicle',
                'panel_title' => 'Private Passenger Vehicle',
                'description' => 'Suitable for privately used passenger cars and standard personal vehicles.',
            ],
            'row_3' => [
                'code' => 'B1',
                'card_title' => 'Pickup 3.5T / Light Utility Vehicle',
                'panel_title' => 'Pickup / Light Utility Vehicle',
                'description' => 'For pickups and light utility vehicles up to 3.5 tonnes.',
            ],
            'row_4' => [
                'code' => 'C under 3.5T',
                'card_title' => 'Minibus',
                'panel_title' => 'Minibus',
                'description' => 'For minibuses and passenger transport vehicles under 3.5 tonnes.',
            ],
            'row_5' => [
                'code' => 'C',
                'card_title' => 'Large Bus / Coaster',
                'panel_title' => 'Large Bus / Coaster',
                'description' => 'For larger passenger buses, coasters, and higher-capacity transport vehicles.',
            ],
            'row_6' => [
                'code' => 'D',
                'card_title' => 'Trucks, Tractors, Semi-trailers & Heavy Utility Vehicles',
                'panel_title' => 'Heavy Utility Vehicle',
                'description' => 'For trucks, tractors, semi-trailers, and heavy utility vehicles.',
            ],
            'row_7' => [
                'code' => 'D',
                'card_title' => 'Other Machinery',
                'panel_title' => 'Other Machinery',
                'description' => 'For special machinery and other equipment requiring technical inspection.',
            ],
        ],
    ],

    'details' => [
        'coverage' => [
            'step' => '2. What your inspection fee covers',
            'items' => [
                [
                    'title' => 'Document Verification',
                    'text' => 'Validation of vehicle identity and applicable inspection category.',
                ],
                [
                    'title' => 'Digital Safety Report',
                    'text' => 'Presentation of the vehicle\'s inspection outcome and findings.',
                ],
                [
                    'title' => 'Advanced Machine Diagnostics',
                    'text' => 'Execution of the required machine-based and visual safety checks.',
                ],
                [
                    'title' => 'Certificate Processing',
                    'text' => 'Official inspection certificate when the vehicle meets applicable requirements.',
                ],
            ],
        ],
        'notice' => [
            'aria_label' => 'Tariff notes and regulatory notice',
            'important' => [
                'title' => 'Important Note',
                'text' => 'Repairs, replacement parts, maintenance work, penalties and other third-party services are not included in the inspection tariff. Any counter-visit conditions or applicable charges will be communicated clearly before the customer returns.',
            ],
            'homologation' => [
                'title' => 'Ministry of Transport Homologation Notice',
                'text' => 'Tariffs follow the applicable vehicle inspection schedule as homologated by the Ministry of Transport.',
                'effective' => 'Effective from: June 1, 2022',
            ],
        ],
        'info' => [
            'step' => '3. Important Information',
            'items' => [
                [
                    'key' => 'payment',
                    'title' => 'Payment',
                    'text' => 'Accepted methods may vary by center. Confirm available options when booking.',
                ],
                [
                    'key' => 'documents',
                    'title' => 'Required Documents',
                    'text' => 'Bring vehicle registration documents and any additional documents required for your vehicle category.',
                ],
                [
                    'key' => 'validity',
                    'title' => 'Validity',
                    'text' => 'Validity depends on the vehicle category as listed in the tariff schedule.',
                ],
                [
                    'key' => 'updates',
                    'title' => 'Regulatory Updates',
                    'text' => 'Tariffs may be revised following official regulatory decisions.',
                ],
            ],
        ],
        'faq' => [
            'step' => '4. FAQs',
            'items' => [
                [
                    'question' => 'How do I know my vehicle category?',
                    'answer' => 'Your category depends on the vehicle type, use, weight, and applicable regulatory classification. Contact a NACHO center when unsure.',
                ],
                [
                    'question' => 'Are the tariffs the same at every NACHO center?',
                    'answer' => 'NACHO will publish the confirmed policy here once officially validated. Please confirm the applicable tariff with your selected center before visiting.',
                ],
                [
                    'question' => 'Does the inspection fee include repairs?',
                    'answer' => 'No. The tariff covers the applicable inspection process, not repairs or replacement parts.',
                ],
                [
                    'question' => 'What happens if my vehicle does not pass?',
                    'answer' => 'The inspection report identifies the outcome and the next steps. Any counter-visit requirements should be explained by the center.',
                ],
                [
                    'question' => 'Can tariffs change?',
                    'answer' => 'Yes. Published tariffs may be updated following applicable regulatory decisions.',
                ],
            ],
        ],
    ],
];
