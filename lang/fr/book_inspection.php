<?php

return [
    'meta' => [
        'title' => 'Réserver une inspection',
    ],

    'hero' => [
        'eyebrow' => 'Réserver une inspection',
        'title' => 'Demandez votre inspection automobile',
        'subtitle' => 'Choisissez votre service, le centre souhaité, les détails du véhicule et l’heure de visite. Le centre sélectionné examinera votre demande avant de confirmer le rendez-vous.',
        'notice' => 'La date et l’heure sélectionnées sont des préférences et restent soumises à la confirmation du centre.',
    ],

    'feedback' => [
        'success_title' => 'Demande de réservation reçue',
        'success_body' => 'Votre référence de demande est :reference. Conservez-la pour le suivi avec le centre sélectionné.',
        'error_title' => 'Veuillez vérifier le formulaire de réservation',
    ],

    'services' => [
        'periodic' => 'Inspection technique périodique',
        'light' => 'Inspection véhicule léger',
        'heavy' => 'Inspection poids lourd',
        'counter' => 'Contre-visite / réinspection',
        'pre_purchase' => 'Inspection avant achat',
    ],

    'form' => [
        'required' => 'obligatoire',
        'optional' => 'facultatif',
        'inspection_details' => 'Détails de l’inspection',
        'visit_details' => 'Visite & coordonnées',
        'service' => [
            'label' => 'Service',
            'placeholder' => 'Sélectionner un service d’inspection',
        ],
        'center' => [
            'label' => 'Centre souhaité',
            'placeholder' => 'Sélectionner un centre',
        ],
        'registration' => [
            'label' => 'Numéro d’immatriculation',
            'placeholder' => 'ex. NW123AN',
            'hint' => 'Entrez le numéro complet d’immatriculation.',
            'sample_plate' => 'NW 123 AN',
        ],
        'category' => [
            'label' => 'Catégorie du véhicule',
            'placeholder' => 'Sélectionner une catégorie',
            'validity' => 'Validité',
        ],
        'previous_reference' => [
            'label' => 'Référence de l’inspection précédente',
            'placeholder' => 'ex. NVI-2024-00123',
            'none' => 'Je n’ai pas ma référence précédente disponible',
        ],
        'date' => [
            'label' => 'Date souhaitée',
            'placeholder' => 'Sélectionner une date',
            'previous_month' => 'Mois précédent',
            'next_month' => 'Mois suivant',
            'weekdays' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
        ],
        'time' => [
            'label' => 'Heure d’arrivée souhaitée',
            'placeholder' => 'Sélectionner une heure',
            'hour_placeholder' => 'Heure',
            'minute_placeholder' => 'Minute',
        ],
        'full_name' => [
            'label' => 'Nom complet',
            'placeholder' => 'Entrez votre nom complet',
        ],
        'phone' => [
            'label' => 'Numéro de téléphone',
            'placeholder' => '6 78 45 67 89',
        ],
        'email' => [
            'label' => 'Adresse e-mail',
            'placeholder' => 'exemple@email.com',
        ],
        'additional_information' => [
            'label' => 'Informations complémentaires',
            'placeholder' => 'Un détail à nous signaler ?',
        ],
        'consent' => 'J’accepte que NACHO utilise les informations fournies pour traiter et répondre à ma demande de réservation.',
        'submit' => 'Envoyer la demande d’inspection',
        'payment_note' => 'Aucun paiement en ligne n’est requis à cette étape.',
    ],

    'validation' => [
        'center_unavailable' => 'Veuillez choisir un centre qui accepte actuellement les demandes de réservation en ligne.',
        'service_unavailable' => 'Veuillez choisir un service d’inspection actif.',
        'service_center_unavailable' => 'Le service sélectionné n’est pas actuellement réservable dans le centre sélectionné.',
        'tariff_unavailable' => 'Veuillez choisir une catégorie de véhicule publiée.',
        'consent_accepted' => 'Veuillez accepter le consentement de réservation avant l’envoi.',
        'preferred_time' => 'Veuillez choisir une heure d’arrivée disponible.',
        'phone' => 'Veuillez saisir un numéro de téléphone valide.',
    ],

    'storage' => [
        'previous_reference' => 'Référence de l’inspection précédente : :reference',
        'previous_reference_unavailable' => 'Référence de l’inspection précédente indisponible.',
    ],

    'summary' => [
        'title' => 'Votre demande d’inspection',
        'not_selected' => 'Pas encore sélectionné',
        'service' => 'Service',
        'center' => 'Centre',
        'vehicle_category' => 'Catégorie du véhicule',
        'published_tariff' => 'Tarif publié',
        'preferred_date' => 'Date souhaitée',
        'preferred_time' => 'Heure souhaitée',
        'secure_title' => 'Votre demande est sécurisée',
        'secure_text' => 'Nous protégeons vos informations et ne les partageons jamais avec des tiers.',
    ],

    'support' => [
        'title' => 'Besoin d’aide ?',
        'tariffs' => [
            'title' => 'Vous ne savez pas quel service ou quelle catégorie choisir ?',
            'button' => 'Voir les tarifs & guide',
        ],
        'contacts' => [
            'title' => 'Besoin d’aide ou vous avez des questions ?',
            'button' => 'Voir les contacts des centres',
        ],
    ],

    'benefits' => [
        'title' => 'Pourquoi réserver avec NACHO ?',
        'items' => [
            'professional' => 'Services d’inspection professionnels',
            'assessment' => 'Évaluation précise & équitable',
            'pricing' => 'Tarification transparente',
            'standards' => 'Standards de qualité nationaux',
        ],
    ],
];
