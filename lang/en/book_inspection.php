<?php

return [
    'meta' => [
        'title' => 'Book an Inspection',
    ],

    'hero' => [
        'eyebrow' => 'Book an Inspection',
        'title' => 'Request Your Vehicle Inspection',
        'subtitle' => 'Choose your service, preferred center, vehicle details, and visit time. The selected center will review your request before confirming the appointment.',
        'notice' => 'Your selected date and time are preferences and remain subject to center confirmation.',
    ],

    'services' => [
        'periodic' => 'Periodic Technical Inspection',
        'light' => 'Light Vehicle Inspection',
        'heavy' => 'Heavy Vehicle Inspection',
        'counter' => 'Counter-Visit / Re-inspection',
        'pre_purchase' => 'Pre-Purchase Inspection',
    ],

    'form' => [
        'required' => 'required',
        'optional' => 'optional',
        'inspection_details' => 'Inspection Details',
        'visit_details' => 'Visit & Contact Details',
        'service' => [
            'label' => 'Service',
            'placeholder' => 'Select an inspection service',
        ],
        'center' => [
            'label' => 'Preferred Center',
            'placeholder' => 'Select a center',
        ],
        'registration' => [
            'label' => 'Vehicle Registration Number',
            'placeholder' => 'e.g. NW123AN',
            'hint' => 'Enter your full registration number.',
            'sample_plate' => 'NW 123 AN',
        ],
        'category' => [
            'label' => 'Vehicle Category',
            'placeholder' => 'Select a category',
            'validity' => 'Validity',
        ],
        'previous_reference' => [
            'label' => 'Previous inspection reference',
            'placeholder' => 'e.g. NVI-2024-00123',
            'none' => 'I do not have my previous reference available',
        ],
        'date' => [
            'label' => 'Preferred Date',
            'placeholder' => 'Select a date',
            'previous_month' => 'Previous month',
            'next_month' => 'Next month',
            'weekdays' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ],
        'time' => [
            'label' => 'Preferred Arrival Time',
            'placeholder' => 'Select a time',
            'hour_placeholder' => 'Hour',
            'minute_placeholder' => 'Minute',
        ],
        'full_name' => [
            'label' => 'Full Name',
            'placeholder' => 'Enter your full name',
        ],
        'phone' => [
            'label' => 'Phone Number',
            'placeholder' => '6 78 45 67 89',
        ],
        'email' => [
            'label' => 'Email Address',
            'placeholder' => 'example@email.com',
        ],
        'additional_information' => [
            'label' => 'Additional Information',
            'placeholder' => 'Any details we should know?',
        ],
        'consent' => 'I agree that NACHO may use the information provided to process and respond to my booking request.',
        'submit' => 'Submit Inspection Request',
        'payment_note' => 'No online payment is required at this stage.',
    ],

    'summary' => [
        'title' => 'Your Inspection Request',
        'not_selected' => 'Not selected yet',
        'service' => 'Service',
        'center' => 'Center',
        'vehicle_category' => 'Vehicle Category',
        'published_tariff' => 'Published Tariff',
        'preferred_date' => 'Preferred Date',
        'preferred_time' => 'Preferred Time',
        'secure_title' => 'Your request is secure',
        'secure_text' => 'We protect your information and never share it with third parties.',
    ],

    'support' => [
        'title' => 'Need Assistance?',
        'tariffs' => [
            'title' => 'Not sure which service or category to choose?',
            'button' => 'View Tariffs & Guide',
        ],
        'contacts' => [
            'title' => 'Need help or have questions?',
            'button' => 'View Center Contacts',
        ],
    ],

    'benefits' => [
        'title' => 'Why Book With NACHO?',
        'items' => [
            'professional' => 'Professional Inspection Services',
            'assessment' => 'Accurate & Fair Assessment',
            'pricing' => 'Transparent Pricing',
            'standards' => 'Nationwide Quality Standards',
        ],
    ],
];
