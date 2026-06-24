<?php

return [
    'meta_title' => 'Contact NACHO Vehicle Inspection',

    'hero' => [
        'title' => 'Contact NACHO Vehicle Inspection',
        'subtitle' => 'Reach our team, locate your nearest inspection center, or request assistance for your vehicle inspection appointment.',
        'actions_label' => 'Contact options',
        'actions' => [
            'book' => [
                'title' => 'Book an Inspection',
                'text' => 'Request an appointment at your preferred center.',
            ],
            'station' => [
                'title' => 'Speak to a Station',
                'text' => 'Contact one of our operational inspection centers.',
            ],
            'enquiry' => [
                'title' => 'General Enquiry',
                'text' => 'Send a question, complaint, feedback, or request.',
            ],
            'hq' => [
                'title' => 'Administrative & HQ',
                'text' => "Reach NACHO's main administrative office.",
            ],
        ],
    ],

    'feedback' => [
        'success_title' => 'Message sent',
        'success_body' => 'Thank you. Your message has been received and will be reviewed by the NACHO team.',
        'error_title' => 'Please review the contact form',
    ],

    'centers' => [
        'title' => 'Find and Contact Your Nearest NACHO Center',
        'headquarters' => 'Headquarters',
        'region' => 'Region',
        'call_center' => 'Call Center',
        'call_hub' => 'Call Hub',
        'maps' => 'View on Google Maps',
        'hq_note' => "This location serves as both an operational inspection center and NACHO's main administrative headquarters.",
        'coming_label' => 'Centers in expansion phase',
        'expansion_phase' => 'Expansion Phase',
        'coming_before' => 'Coming before November 2026',
        'map' => [
            'label' => 'Center map',
            'title' => 'Interactive Center Map',
            'hq_label' => 'Nacho-Bamenda / HQ',
            'loading' => 'Loading interactive map...',
            'approximate' => 'Approximate city marker',
        ],
    ],

    'form' => [
        'title' => 'Send a message',
        'subtitle' => "Fill in the form below and we'll get back to you.",
        'fields' => [
            'full_name' => 'Full Name',
            'phone' => 'Phone Number',
            'email' => 'Email Address',
            'center' => 'Preferred Center',
            'reason' => 'Reason for Contact',
            'message' => 'Message',
            'website' => 'Website',
        ],
        'placeholders' => [
            'full_name' => 'Enter your full name',
            'phone' => 'e.g. (+237) 6XX XXX XXX',
            'email' => 'Enter your email address',
            'center' => 'Select a center',
            'reason' => 'Select a reason',
            'message' => 'Type your message here...',
        ],
        'reasons' => [
            'Booking Assistance',
            'General Enquiry',
            'Center Information',
            'Complaint & Feedback',
            'Corporate Partnership',
            'Careers',
        ],
        'consent' => 'I agree that NACHO may use the information provided to respond to my request.',
        'submit' => 'Send Message',
    ],

    'validation' => [
        'center_unavailable' => 'Please choose an operational NACHO center.',
        'reason_unavailable' => 'Please choose a valid reason for contacting NACHO.',
        'consent_accepted' => 'Please accept the contact consent statement before submitting.',
        'phone' => 'Please enter a valid phone number.',
    ],

    'storage' => [
        'subject' => ':reason - :center',
        'preferred_center' => 'Preferred center: :center',
        'reason' => 'Reason: :reason',
    ],

    'next' => [
        'title' => 'What Happens Next?',
        'subtitle' => 'Here’s how we handle your request.',
        'steps' => [
            [
                'icon' => 'clipboard-list',
                'title' => 'Message Logged',
                'text' => 'Your request is routed to the appropriate center or administrative team.',
            ],
            [
                'icon' => 'user-round',
                'title' => 'Expert Review',
                'text' => 'A NACHO staff member reviews your request and checks the required details.',
            ],
            [
                'icon' => 'mail',
                'title' => 'Direct Follow-up',
                'text' => 'You receive a response by phone or email during normal working hours.',
            ],
        ],
        'note' => 'Responses are typically provided during normal working hours.',
    ],

    'faq' => [
        'title' => 'Frequently Asked Questions',
        'items' => [
            [
                'question' => 'Do I need to book before coming?',
                'answer' => 'You may request an appointment online or contact your nearest center for guidance.',
            ],
            [
                'question' => 'Which center should I choose?',
                'answer' => 'Choose the center closest to your location or the one most convenient for your schedule.',
            ],
            [
                'question' => 'Can I get directions to a center?',
                'answer' => 'Yes. Each operational center has a “View on Google Maps” button.',
            ],
            [
                'question' => 'What information should I provide when contacting NACHO?',
                'answer' => 'Your name, phone number, preferred center, and the reason for your request are enough.',
            ],
            [
                'question' => 'Are Douala and Kumba already operational?',
                'answer' => 'No. They are expansion centers coming soon before November 2026.',
            ],
        ],
    ],
];
