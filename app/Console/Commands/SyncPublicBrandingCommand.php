<?php

namespace App\Console\Commands;

use Database\Seeders\CenterSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Console\Command;

class SyncPublicBrandingCommand extends Command
{
    protected $signature = 'site:sync-public-branding';

    protected $description = 'Refresh public center names, contacts, hours, and site settings from seeders (safe for production; does not re-seed users or roles)';

    public function handle(): int
    {
        $this->components->info('Syncing inspection centers and contacts…');
        $this->callSilent('db:seed', [
            '--class' => CenterSeeder::class,
            '--force' => true,
        ]);

        $this->components->info('Syncing site-wide contact settings…');
        $this->callSilent('db:seed', [
            '--class' => SiteSettingSeeder::class,
            '--force' => true,
        ]);

        $this->components->success('Public branding data synced. Clear config/view caches on the server if you use them.');

        return self::SUCCESS;
    }
}
