<?php

return [
    'meta' => [
        'title' => 'Tarifs d\'inspection',
    ],

    'hero' => [
        'eyebrow' => 'Tarifs d\'inspection',
        'title' => 'Tarifs transparents de contrôle technique',
        'subtitle' => 'Trouvez le tarif applicable et la période de validité selon la catégorie de votre véhicule avant de vous rendre dans un centre NACHO.',
        'proof_label' => 'Points clés des tarifs',
        'proof' => [
            'categories' => 'Catégories de véhicules claires',
            'published' => 'Frais d\'inspection publiés',
            'no_hidden' => 'Aucune information cachée',
        ],
        'notice' => [
            'title' => 'Avis officiel sur les tarifs',
            'text' => 'Les tarifs affichés suivent le barème applicable au contrôle technique automobile et peuvent être révisés selon les décisions réglementaires officielles.',
        ],
        'meta' => [
            'last_verified_label' => 'Dernière vérification :',
            'last_verified' => '20 mai 2024',
            'effective_label' => 'En vigueur depuis :',
            'effective' => '1er juin 2022',
            'notice_link' => 'Voir l\'avis tarifaire',
        ],
    ],

    'console' => [
        'step' => '1. Trouver votre tarif',
        'title' => 'Trouvez le tarif de votre véhicule',
        'subtitle' => 'Sélectionnez le type de véhicule qui décrit le mieux votre véhicule.',
        'category_label' => 'Catégories tarifaires de véhicules',
        'selected_label' => 'Catégorie sélectionnée',
        'mobile_select_label' => 'Catégorie de véhicule',
        'fee_label' => 'Frais d\'inspection',
        'validity_label' => 'Période de validité',
        'book_category' => 'Réserver cette catégorie',
        'documents' => 'Voir les documents requis',
        'show_all' => 'Afficher tous les tarifs',
        'help' => [
            'text' => 'Vous ne savez pas quelle catégorie s\'applique à votre véhicule ?',
            'link' => 'Demander à un centre NACHO',
        ],
        'rows' => [
            'row_1' => [
                'code' => 'A',
                'card_title' => 'Taxi / Auto-école',
                'panel_title' => 'Taxi / Auto-école',
                'description' => 'Pour les taxis, véhicules d\'auto-école et véhicules soumis à un contrôle fréquent de service public.',
            ],
            'row_2' => [
                'code' => 'B',
                'card_title' => 'Véhicule particulier',
                'panel_title' => 'Véhicule particulier',
                'description' => 'Adapté aux voitures particulières à usage privé et aux véhicules personnels standards.',
            ],
            'row_3' => [
                'code' => 'B1',
                'card_title' => 'Pick-up 3,5 T / Utilitaire léger',
                'panel_title' => 'Pick-up / Utilitaire léger',
                'description' => 'Pour les pick-up et véhicules utilitaires légers jusqu\'à 3,5 tonnes.',
            ],
            'row_4' => [
                'code' => 'C moins de 3,5 T',
                'card_title' => 'Mini-bus',
                'panel_title' => 'Mini-bus',
                'description' => 'Pour les mini-bus et véhicules de transport de personnes de moins de 3,5 tonnes.',
            ],
            'row_5' => [
                'code' => 'C',
                'card_title' => 'Grand bus / Coaster',
                'panel_title' => 'Grand bus / Coaster',
                'description' => 'Pour les grands bus, coasters et véhicules de transport à plus grande capacité.',
            ],
            'row_6' => [
                'code' => 'D',
                'card_title' => 'Camions, tracteurs, semi-remorques & utilitaires lourds',
                'panel_title' => 'Véhicule utilitaire lourd',
                'description' => 'Pour les camions, tracteurs, semi-remorques et véhicules utilitaires lourds.',
            ],
            'row_7' => [
                'code' => 'D',
                'card_title' => 'Autres engins',
                'panel_title' => 'Autres engins',
                'description' => 'Pour les engins spéciaux et autres équipements nécessitant un contrôle technique.',
            ],
        ],
    ],

    'details' => [
        'coverage' => [
            'step' => '2. Ce que couvrent vos frais d\'inspection',
            'items' => [
                [
                    'title' => 'Vérification des documents',
                    'text' => 'Validation de l\'identité du véhicule et de la catégorie d\'inspection applicable.',
                ],
                [
                    'title' => 'Rapport de sécurité numérique',
                    'text' => 'Présentation du résultat de l\'inspection du véhicule et des constats.',
                ],
                [
                    'title' => 'Diagnostics avancés sur machines',
                    'text' => 'Réalisation des contrôles requis sur machines et des vérifications visuelles de sécurité.',
                ],
                [
                    'title' => 'Traitement du certificat',
                    'text' => 'Certificat officiel d\'inspection lorsque le véhicule respecte les exigences applicables.',
                ],
            ],
        ],
        'notice' => [
            'aria_label' => 'Notes tarifaires et avis réglementaire',
            'important' => [
                'title' => 'Note importante',
                'text' => 'Les réparations, pièces de rechange, travaux d\'entretien, pénalités et autres services tiers ne sont pas inclus dans le tarif d\'inspection. Les conditions de contre-visite ou frais applicables seront communiqués clairement avant le retour du client.',
            ],
            'homologation' => [
                'title' => 'Avis d\'homologation du Ministère des Transports',
                'text' => 'Les tarifs suivent le barème applicable au contrôle technique automobile homologué par le Ministère des Transports.',
                'effective' => 'En vigueur depuis : 1er juin 2022',
            ],
        ],
        'info' => [
            'step' => '3. Informations importantes',
            'items' => [
                [
                    'key' => 'payment',
                    'title' => 'Paiement',
                    'text' => 'Les moyens acceptés peuvent varier selon le centre. Confirmez les options disponibles lors de la réservation.',
                ],
                [
                    'key' => 'documents',
                    'title' => 'Documents requis',
                    'text' => 'Présentez les documents d\'immatriculation du véhicule et tout document supplémentaire requis pour votre catégorie.',
                ],
                [
                    'key' => 'validity',
                    'title' => 'Validité',
                    'text' => 'La validité dépend de la catégorie du véhicule indiquée dans la grille tarifaire.',
                ],
                [
                    'key' => 'updates',
                    'title' => 'Mises à jour réglementaires',
                    'text' => 'Les tarifs peuvent être révisés à la suite de décisions réglementaires officielles.',
                ],
            ],
        ],
        'faq' => [
            'step' => '4. FAQ',
            'items' => [
                [
                    'question' => 'Comment connaître la catégorie de mon véhicule ?',
                    'answer' => 'Votre catégorie dépend du type de véhicule, de son usage, de son poids et de la classification réglementaire applicable. Contactez un centre NACHO en cas de doute.',
                ],
                [
                    'question' => 'Les tarifs sont-ils les mêmes dans tous les centres NACHO ?',
                    'answer' => 'NACHO publiera ici la politique confirmée dès sa validation officielle. Veuillez confirmer le tarif applicable auprès du centre choisi avant votre visite.',
                ],
                [
                    'question' => 'Les frais d\'inspection incluent-ils les réparations ?',
                    'answer' => 'Non. Le tarif couvre le processus d\'inspection applicable, pas les réparations ni les pièces de rechange.',
                ],
                [
                    'question' => 'Que se passe-t-il si mon véhicule ne passe pas ?',
                    'answer' => 'Le rapport d\'inspection indique le résultat et les étapes suivantes. Les exigences de contre-visite doivent être expliquées par le centre.',
                ],
                [
                    'question' => 'Les tarifs peuvent-ils changer ?',
                    'answer' => 'Oui. Les tarifs publiés peuvent être mis à jour à la suite de décisions réglementaires applicables.',
                ],
            ],
        ],
    ],

    'dynamic' => [
        'category' => 'Catégorie :code',
        'documents' => 'Carte grise, assurance (voir page Tarifs)',
        'validity_units' => [
            'days' => 'jours',
            'months' => 'mois',
            'years' => 'ans',
        ],
        'test_types' => [
            'all' => 'Tous',
            'all_except_suspension' => 'Tous sauf suspension',
        ],
    ],
];
