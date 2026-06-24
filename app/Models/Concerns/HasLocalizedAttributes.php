<?php

namespace App\Models\Concerns;

trait HasLocalizedAttributes
{
    /**
     * Resolve a bilingual attribute using the current app locale with FR fallback.
     */
    public function localized(string $attribute, ?string $locale = null, string $fallbackLocale = 'fr'): mixed
    {
        $locale = $locale ?: app()->getLocale();
        $localizedKey = "{$attribute}_{$locale}";
        $fallbackKey = "{$attribute}_{$fallbackLocale}";

        return $this->getAttribute($localizedKey)
            ?: $this->getAttribute($fallbackKey)
            ?: $this->getAttribute("{$attribute}_en");
    }
}
