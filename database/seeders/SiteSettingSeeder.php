<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSettingSeeder extends Seeder
{
    /**
     * Seed editable site-wide settings.
     */
    public function run(): void
    {
        $now = now();

        $settings = [
            ['key' => 'site_name', 'value' => 'NACHO Vehicle Inspection', 'type' => 'text'],
            ['key' => 'default_language', 'value' => 'fr', 'type' => 'text'],
            ['key' => 'contact_email', 'value' => 'nachovehicletestingstation@yahoo.com', 'type' => 'text'],
            ['key' => 'contact_phone', 'value' => '(+237) 675615478', 'type' => 'text'],
            ['key' => 'address', 'value' => 'Atuakum Mankon, P.O. Box 100 Bamenda, Cameroon', 'type' => 'text'],
            ['key' => 'postal_box', 'value' => 'P.O. Box 100 Bamenda', 'type' => 'text'],
            ['key' => 'logo', 'value' => 'images/nacho-logo.png', 'type' => 'image'],
            ['key' => 'footer_text_en', 'value' => 'Drive Safe. Stay Compliant. Trust NACHO.', 'type' => 'text'],
            ['key' => 'footer_text_fr', 'value' => 'Roulez en securite. Restez conforme. Faites confiance a NACHO.', 'type' => 'text'],
            ['key' => 'facebook_url', 'value' => null, 'type' => 'text'],
            ['key' => 'whatsapp_contact', 'value' => null, 'type' => 'text'],
            ['key' => 'primary_color', 'value' => '#b45309', 'type' => 'color'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'tariff_logistics_payment_en', 'value' => 'Accepted payment methods may vary by center. Confirm available options when booking.', 'type' => 'text'],
            ['key' => 'tariff_logistics_payment_fr', 'value' => 'Les moyens de paiement acceptes peuvent varier selon le centre. Confirmez les options lors de la reservation.', 'type' => 'text'],
            ['key' => 'tariff_logistics_documents_en', 'value' => 'Bring vehicle registration documents and any additional documents required for your vehicle category.', 'type' => 'text'],
            ['key' => 'tariff_logistics_documents_fr', 'value' => 'Presentez les documents d immatriculation et les pieces requises selon votre categorie.', 'type' => 'text'],
            ['key' => 'careers_general_application_email', 'value' => null, 'type' => 'text'],
            ['key' => 'careers_recruitment_safety_notice_en', 'value' => null, 'type' => 'text'],
            ['key' => 'careers_recruitment_safety_notice_fr', 'value' => null, 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
