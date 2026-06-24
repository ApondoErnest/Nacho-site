<?php

return [
    'meta_title' => 'Contacter NACHO Vehicle Inspection',

    'hero' => [
        'title' => 'Contacter NACHO Vehicle Inspection',
        'subtitle' => 'Joignez notre équipe, trouvez le centre d\'inspection le plus proche ou demandez de l\'aide pour votre rendez-vous.',
        'actions_label' => 'Options de contact',
        'actions' => [
            'book' => [
                'title' => 'Réserver une inspection',
                'text' => 'Demandez un rendez-vous dans le centre de votre choix.',
            ],
            'station' => [
                'title' => 'Parler à une station',
                'text' => 'Contactez l\'un de nos centres d\'inspection opérationnels.',
            ],
            'enquiry' => [
                'title' => 'Demande générale',
                'text' => 'Envoyez une question, une réclamation, un avis ou une demande.',
            ],
            'hq' => [
                'title' => 'Administration & siège',
                'text' => 'Contactez le bureau administratif principal de NACHO.',
            ],
        ],
    ],

    'feedback' => [
        'success_title' => 'Message envoyé',
        'success_body' => 'Merci. Votre message a été reçu et sera examiné par l’équipe NACHO.',
        'error_title' => 'Veuillez vérifier le formulaire de contact',
    ],

    'centers' => [
        'title' => 'Trouvez et contactez le centre NACHO le plus proche',
        'headquarters' => 'Siège',
        'region' => 'Région',
        'call_center' => 'Appeler le centre',
        'call_hub' => 'Appeler le siège',
        'maps' => 'Voir sur Google Maps',
        'hq_note' => 'Ce site sert à la fois de centre d\'inspection opérationnel et de siège administratif principal de NACHO.',
        'coming_label' => 'Centres en phase d\'extension',
        'expansion_phase' => 'Phase d\'extension',
        'coming_before' => 'Ouverture avant novembre 2026',
        'map' => [
            'label' => 'Carte des centres',
            'title' => 'Carte interactive des centres',
            'hq_label' => 'Nacho-Bamenda / Siège',
            'loading' => 'Chargement de la carte interactive...',
            'approximate' => 'Repère approximatif de la ville',
        ],
    ],

    'form' => [
        'title' => 'Envoyer un message',
        'subtitle' => 'Remplissez le formulaire ci-dessous et nous vous répondrons.',
        'fields' => [
            'full_name' => 'Nom complet',
            'phone' => 'Numéro de téléphone',
            'email' => 'Adresse e-mail',
            'center' => 'Centre souhaité',
            'reason' => 'Motif du contact',
            'message' => 'Message',
            'website' => 'Site web',
        ],
        'placeholders' => [
            'full_name' => 'Entrez votre nom complet',
            'phone' => 'ex. (+237) 6XX XXX XXX',
            'email' => 'Entrez votre adresse e-mail',
            'center' => 'Sélectionner un centre',
            'reason' => 'Sélectionner un motif',
            'message' => 'Écrivez votre message ici...',
        ],
        'reasons' => [
            'Aide à la réservation',
            'Demande générale',
            'Information sur un centre',
            'Réclamation & avis',
            'Partenariat d’entreprise',
            'Carrières',
        ],
        'consent' => 'J’accepte que NACHO utilise les informations fournies pour répondre à ma demande.',
        'submit' => 'Envoyer le message',
    ],

    'validation' => [
        'center_unavailable' => 'Veuillez choisir un centre NACHO opérationnel.',
        'reason_unavailable' => 'Veuillez choisir un motif de contact valide.',
        'consent_accepted' => 'Veuillez accepter le consentement de contact avant l’envoi.',
        'phone' => 'Veuillez saisir un numéro de téléphone valide.',
    ],

    'storage' => [
        'subject' => ':reason - :center',
        'preferred_center' => 'Centre souhaité : :center',
        'reason' => 'Motif : :reason',
    ],

    'next' => [
        'title' => 'Que se passe-t-il ensuite ?',
        'subtitle' => 'Voici comment nous traitons votre demande.',
        'steps' => [
            [
                'icon' => 'clipboard-list',
                'title' => 'Message enregistré',
                'text' => 'Votre demande est transmise au centre ou à l’équipe administrative concernée.',
            ],
            [
                'icon' => 'user-round',
                'title' => 'Examen par un expert',
                'text' => 'Un membre de l’équipe NACHO examine votre demande et vérifie les détails requis.',
            ],
            [
                'icon' => 'mail',
                'title' => 'Suivi direct',
                'text' => 'Vous recevez une réponse par téléphone ou par e-mail pendant les heures ouvrables.',
            ],
        ],
        'note' => 'Les réponses sont généralement fournies pendant les heures normales de travail.',
    ],

    'faq' => [
        'title' => 'Questions fréquentes',
        'items' => [
            [
                'question' => 'Dois-je réserver avant de venir ?',
                'answer' => 'Vous pouvez demander un rendez-vous en ligne ou contacter le centre le plus proche pour obtenir des conseils.',
            ],
            [
                'question' => 'Quel centre dois-je choisir ?',
                'answer' => 'Choisissez le centre le plus proche de votre position ou celui qui convient le mieux à votre emploi du temps.',
            ],
            [
                'question' => 'Puis-je obtenir un itinéraire vers un centre ?',
                'answer' => 'Oui. Chaque centre opérationnel dispose d’un bouton « Voir sur Google Maps ».',
            ],
            [
                'question' => 'Quelles informations dois-je fournir en contactant NACHO ?',
                'answer' => 'Votre nom, votre numéro de téléphone, le centre souhaité et le motif de votre demande suffisent.',
            ],
            [
                'question' => 'Douala et Kumba sont-ils déjà opérationnels ?',
                'answer' => 'Non. Ce sont des centres d’extension dont l’ouverture est prévue avant novembre 2026.',
            ],
        ],
    ],
];
