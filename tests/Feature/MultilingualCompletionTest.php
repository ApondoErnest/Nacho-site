<?php

namespace Tests\Feature;

use App\Enums\CenterStatus;
use App\Models\Center;
use App\Models\Service;
use App\Models\Tariff;
use App\Support\PublicSiteData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultilingualCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_translation_files_have_matching_french_and_english_keys(): void
    {
        foreach (glob(lang_path('fr/*.php')) as $frFile) {
            $name = basename($frFile);
            $enFile = lang_path("en/{$name}");

            $this->assertFileExists($enFile, "Missing English translation file for {$name}.");

            $frKeys = $this->flattenTranslationKeys(require $frFile);
            $enKeys = $this->flattenTranslationKeys(require $enFile);

            $this->assertSame([], array_values(array_diff($frKeys, $enKeys)), "Missing English keys in {$name}.");
            $this->assertSame([], array_values(array_diff($enKeys, $frKeys)), "Missing French keys in {$name}.");
        }
    }

    public function test_public_requests_default_to_french_and_can_switch_to_english(): void
    {
        $this->assertSame('fr', config('app.locale'));
        $this->assertSame('fr', config('app.fallback_locale'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="fr"', false)
            ->assertSee(__('components.hero.title'));

        $this->from(route('about'))
            ->get(route('language.switch', 'en'))
            ->assertRedirect(route('about'))
            ->assertSessionHas('locale', 'en');

        $this->withSession(['locale' => 'en'])
            ->get(route('about'))
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('About NACHO');
    }

    public function test_auth_scaffold_uses_french_json_strings_by_default(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Se connecter')
            ->assertSee('Mot de passe')
            ->assertSee('Se souvenir de moi')
            ->assertDontSee('Remember me');

        $this->withSession(['locale' => 'en'])
            ->get(route('login'))
            ->assertOk()
            ->assertSee('Log in')
            ->assertSee('Remember me');
    }

    public function test_database_content_uses_selected_locale_with_french_fallback(): void
    {
        $center = Center::factory()->create([
            'name_en' => 'Localized English Center',
            'name_fr' => 'Centre localise francais',
            'city_en' => 'English City',
            'city_fr' => 'Ville francaise',
            'status' => CenterStatus::ACTIVE->value,
            'booking_enabled' => true,
        ]);

        $localizedService = Service::factory()->create([
            'slug' => 'localized-service',
            'title_en' => 'Localized English Service',
            'title_fr' => 'Service localise francais',
        ]);

        $fallbackService = Service::factory()->create([
            'slug' => 'fallback-service',
            'title_en' => '',
            'title_fr' => 'Service secours francais',
        ]);

        $center->services()->attach($localizedService->id, [
            'is_available' => true,
            'booking_enabled' => true,
        ]);
        $center->services()->attach($fallbackService->id, [
            'is_available' => true,
            'booking_enabled' => true,
        ]);

        $this->get(route('centers.index'))
            ->assertOk()
            ->assertSee('Centre localise francais')
            ->assertSee('Service localise francais')
            ->assertDontSee('Localized English Center');

        $this->withSession(['locale' => 'en'])
            ->get(route('centers.index'))
            ->assertOk()
            ->assertSee('Localized English Center')
            ->assertSee('Localized English Service')
            ->assertSee('Service secours francais')
            ->assertDontSee('Centre localise francais');
    }

    public function test_generated_public_tariff_labels_are_translated(): void
    {
        Tariff::factory()->create([
            'category_code' => 'Z',
            'category_slug' => 'category-d-other-engins',
            'name_en' => 'Special Machinery',
            'name_fr' => 'Engins speciaux',
            'price_fcfa' => 99000,
            'validity_value' => 12,
            'validity_unit' => 'months',
            'is_active' => true,
            'is_bookable' => true,
            'display_order' => 1,
        ]);

        $row = app(PublicSiteData::class)->tariffPreview()->first();

        $this->assertSame('Category Z', $row['category_en']);
        $this->assertSame('Catégorie Z', $row['category_fr']);
        $this->assertSame('12 months', $row['validity_en']);
        $this->assertSame('12 mois', $row['validity_fr']);
        $this->assertSame('All except Suspension', $row['test_type_en']);
        $this->assertSame('Tous sauf suspension', $row['test_type_fr']);
        $this->assertSame('Registration, insurance (see full tariffs page)', $row['documents_en']);
        $this->assertSame('Carte grise, assurance (voir page Tarifs)', $row['documents_fr']);
    }

    /**
     * @return array<int, string>
     */
    private function flattenTranslationKeys(array $translations, string $prefix = ''): array
    {
        $keys = [];

        foreach ($translations as $key => $value) {
            $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                array_push($keys, ...$this->flattenTranslationKeys($value, $path));

                continue;
            }

            $keys[] = $path;
        }

        sort($keys);

        return $keys;
    }
}
