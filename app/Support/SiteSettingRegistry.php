<?php

namespace App\Support;

use App\Enums\SettingType;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;

class SiteSettingRegistry
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function definitions(): array
    {
        return [
            'site_name' => [
                'label' => 'Site name',
                'group' => 'General',
                'help' => 'Used as the main website name in admin and public metadata.',
                'type' => SettingType::TEXT,
                'rules' => ['required', 'string', 'max:255'],
            ],
            'default_language' => [
                'label' => 'Default language',
                'group' => 'General',
                'help' => 'Public language default. Current supported values are fr and en.',
                'type' => SettingType::TEXT,
                'rules' => ['required', 'in:fr,en'],
            ],
            'contact_email' => [
                'label' => 'Contact email',
                'group' => 'Contact',
                'help' => 'Main corporate email shown in public contact surfaces.',
                'type' => SettingType::TEXT,
                'rules' => ['required', 'email:rfc', 'max:255'],
            ],
            'contact_phone' => [
                'label' => 'Contact phone',
                'group' => 'Contact',
                'help' => 'Primary corporate phone shown in public contact surfaces.',
                'type' => SettingType::TEXT,
                'rules' => ['required', 'string', 'max:80'],
            ],
            'address' => [
                'label' => 'Address',
                'group' => 'Contact',
                'help' => 'Corporate address used in the footer and contact context.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:1000'],
                'multiline' => true,
            ],
            'postal_box' => [
                'label' => 'Postal box',
                'group' => 'Contact',
                'help' => 'Postal box shown where corporate contact details are displayed.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:255'],
            ],
            'logo' => [
                'label' => 'Logo path',
                'group' => 'Branding',
                'help' => 'Path to the logo asset, usually from public images or the media library.',
                'type' => SettingType::IMAGE,
                'rules' => ['nullable', 'string', 'max:255'],
            ],
            'primary_color' => [
                'label' => 'Primary color',
                'group' => 'Branding',
                'help' => 'Hex color reserved for future runtime theme overrides.',
                'type' => SettingType::COLOR,
                'rules' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            ],
            'footer_text_en' => [
                'label' => 'Footer text EN',
                'group' => 'Footer',
                'help' => 'English slogan or short footer line.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:500'],
                'multiline' => true,
            ],
            'footer_text_fr' => [
                'label' => 'Footer text FR',
                'group' => 'Footer',
                'help' => 'French slogan or short footer line.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:500'],
                'multiline' => true,
            ],
            'facebook_url' => [
                'label' => 'Facebook URL',
                'group' => 'Social',
                'help' => 'Official Facebook page URL, if available.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'url:http,https', 'max:255'],
            ],
            'whatsapp_contact' => [
                'label' => 'WhatsApp contact',
                'group' => 'Social',
                'help' => 'Official WhatsApp line or wa.me URL, if approved.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:255'],
            ],
            'tariff_logistics_payment_en' => [
                'label' => 'Tariff payment note EN',
                'group' => 'Tariffs',
                'help' => 'English payment guidance used near tariff information.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:1000'],
                'multiline' => true,
            ],
            'tariff_logistics_payment_fr' => [
                'label' => 'Tariff payment note FR',
                'group' => 'Tariffs',
                'help' => 'French payment guidance used near tariff information.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:1000'],
                'multiline' => true,
            ],
            'tariff_logistics_documents_en' => [
                'label' => 'Tariff documents note EN',
                'group' => 'Tariffs',
                'help' => 'English required-documents guidance used near tariff information.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:1000'],
                'multiline' => true,
            ],
            'tariff_logistics_documents_fr' => [
                'label' => 'Tariff documents note FR',
                'group' => 'Tariffs',
                'help' => 'French required-documents guidance used near tariff information.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:1000'],
                'multiline' => true,
            ],
            'careers_general_application_email' => [
                'label' => 'General application email',
                'group' => 'Careers',
                'help' => 'Recruitment email used for general applications. Leave blank until approved.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'email:rfc', 'max:255'],
            ],
            'careers_recruitment_safety_notice_en' => [
                'label' => 'Recruitment safety notice EN',
                'group' => 'Careers',
                'help' => 'English fraud-prevention or recruitment safety notice.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:1000'],
                'multiline' => true,
            ],
            'careers_recruitment_safety_notice_fr' => [
                'label' => 'Recruitment safety notice FR',
                'group' => 'Careers',
                'help' => 'French fraud-prevention or recruitment safety notice.',
                'type' => SettingType::TEXT,
                'rules' => ['nullable', 'string', 'max:1000'],
                'multiline' => true,
            ],
            'maintenance_mode' => [
                'label' => 'Maintenance mode',
                'group' => 'Access',
                'help' => 'Reserved toggle for a future public maintenance middleware.',
                'type' => SettingType::BOOLEAN,
                'rules' => ['boolean'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function definitionFor(string $key, ?SiteSetting $setting = null): array
    {
        $definition = self::definitions()[$key] ?? [
            'label' => str($key)->replace('_', ' ')->title()->toString(),
            'group' => 'Other',
            'help' => 'Custom setting key.',
            'type' => $setting?->type ?? SettingType::TEXT,
            'rules' => ['nullable', 'string', 'max:2000'],
        ];

        $definition['key'] = $key;
        $definition['type'] = $definition['type'] instanceof SettingType
            ? $definition['type']
            : SettingType::tryFrom((string) $definition['type']) ?? SettingType::TEXT;

        return $definition;
    }

    public static function settingsByGroup(): Collection
    {
        $settings = SiteSetting::query()
            ->orderBy('key')
            ->get()
            ->keyBy('key');

        foreach (self::definitions() as $key => $definition) {
            if (! $settings->has($key)) {
                $settings->put($key, new SiteSetting([
                    'key' => $key,
                    'value' => null,
                    'type' => $definition['type']->value,
                ]));
            }
        }

        return $settings
            ->map(function (SiteSetting $setting): array {
                return [
                    'setting' => $setting,
                    'definition' => self::definitionFor($setting->key, $setting),
                ];
            })
            ->sortBy(fn (array $item): string => sprintf(
                '%02d-%s',
                self::groupOrder($item['definition']['group']),
                $item['definition']['key'],
            ))
            ->groupBy(fn (array $item): string => $item['definition']['group']);
    }

    /**
     * @return array<string, int>
     */
    public static function counts(): array
    {
        return [
            'total' => SiteSetting::query()->count(),
            'text' => SiteSetting::query()->where('type', SettingType::TEXT->value)->count(),
            'image' => SiteSetting::query()->where('type', SettingType::IMAGE->value)->count(),
            'boolean' => SiteSetting::query()->where('type', SettingType::BOOLEAN->value)->count(),
            'color' => SiteSetting::query()->where('type', SettingType::COLOR->value)->count(),
        ];
    }

    private static function groupOrder(string $group): int
    {
        return [
            'General' => 1,
            'Contact' => 2,
            'Branding' => 3,
            'Footer' => 4,
            'Social' => 5,
            'Tariffs' => 6,
            'Careers' => 7,
            'Access' => 8,
            'Other' => 99,
        ][$group] ?? 98;
    }
}
