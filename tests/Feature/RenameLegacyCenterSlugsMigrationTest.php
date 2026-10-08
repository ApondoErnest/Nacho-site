<?php

namespace Tests\Feature;

use App\Models\Center;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenameLegacyCenterSlugsMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function runMigration(): void
    {
        $migration = require database_path('migrations/2026_10_08_000001_rename_legacy_center_slugs.php');
        $migration->up();
    }

    public function test_legacy_slug_is_renamed(): void
    {
        $center = Center::factory()->create(['slug' => 'nacho-douala']);

        $this->runMigration();

        $this->assertSame('novetesco-douala', $center->fresh()->slug);
    }

    public function test_rename_is_skipped_when_new_slug_already_exists(): void
    {
        $legacy = Center::factory()->create(['slug' => 'nacho-kumba']);
        Center::factory()->create(['slug' => 'novetesco-kumba']);

        $this->runMigration();

        $this->assertSame('nacho-kumba', $legacy->fresh()->slug);
        $this->assertSame(1, Center::query()->where('slug', 'novetesco-kumba')->count());
    }
}
