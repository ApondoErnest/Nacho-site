<?php

namespace App\Http\Requests\Admin;

use App\Enums\SettingType;
use App\Support\SiteSettingRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteSettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [];

        foreach (SiteSettingRegistry::settingsByGroup()->flatten(1) as $item) {
            $definition = $item['definition'];
            $rules["settings.{$definition['key']}"] = $definition['rules'];
        }

        foreach (SiteSettingRegistry::definitions() as $key => $definition) {
            $rules["types.{$key}"] = [
                'nullable',
                Rule::in(array_map(fn (SettingType $type): string => $type->value, SettingType::cases())),
            ];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $settings = $this->input('settings', []);

        foreach (SiteSettingRegistry::settingsByGroup()->flatten(1) as $item) {
            $setting = $item['setting'];
            $definition = $item['definition'];

            if ($definition['type'] === SettingType::BOOLEAN) {
                $settings[$setting->key] = $this->boolean("settings.{$setting->key}") ? '1' : '0';
            } elseif (array_key_exists($setting->key, $settings) && $settings[$setting->key] === '') {
                $settings[$setting->key] = null;
            }
        }

        $this->merge([
            'settings' => $settings,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function settingValues(): array
    {
        return $this->validated('settings', []);
    }
}
