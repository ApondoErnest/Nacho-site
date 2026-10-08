<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * @var array<string, string>
     */
    private array $renames = [
        'nacho-yaounde' => 'novetesco-yaounde',
        'nacho-nkwen-bamenda' => 'novetesco-nkwen-bamenda',
        'nacho-mankon-bamenda' => 'novetesco-mankon-bamenda',
        'nacho-douala' => 'novetesco-douala',
        'nacho-kumba' => 'novetesco-kumba',
    ];

    /**
     * Rename legacy center slugs; skipped where the new slug already exists (slug is unique).
     */
    public function up(): void
    {
        foreach ($this->renames as $from => $to) {
            $this->rename($from, $to);
        }
    }

    public function down(): void
    {
        foreach ($this->renames as $from => $to) {
            $this->rename($to, $from);
        }
    }

    private function rename(string $from, string $to): void
    {
        if (DB::table('centers')->where('slug', $to)->exists()) {
            return;
        }

        DB::table('centers')->where('slug', $from)->update(['slug' => $to]);
    }
};
