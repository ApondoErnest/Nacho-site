<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * Step 48 — backups, monitoring and logs on the VPS (see deploy/vps/RUNBOOK.md §8).
 */
class Step48OpsScriptsTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function scripts(): array
    {
        return [
            'backup' => ['deploy/vps/backup.sh'],
            'restore' => ['deploy/vps/restore.sh'],
            'monitor' => ['deploy/vps/monitor.sh'],
            'logs' => ['deploy/vps/logs.sh'],
        ];
    }

    #[DataProvider('scripts')]
    public function test_ops_script_is_executable_and_valid_bash(string $script): void
    {
        $path = base_path($script);

        $this->assertFileExists($path);
        $this->assertTrue(is_executable($path), "{$script} must be executable (git update-index --chmod=+x).");
        $this->assertStringContainsString('source "$(dirname "${BASH_SOURCE[0]}")/lib.sh"', (string) file_get_contents($path));

        $process = new Process(['bash', '-n', $path]);
        $process->run();
        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
    }

    public function test_shared_lib_targets_production_compose_files(): void
    {
        $lib = (string) file_get_contents(base_path('deploy/vps/lib.sh'));

        $this->assertStringContainsString('docker-compose.production.yml', $lib);
        $this->assertStringContainsString('.env.production', $lib);
        $this->assertStringContainsString('.env.ops', $lib);
    }

    public function test_backup_dumps_consistently_and_verifies_output(): void
    {
        $backup = (string) file_get_contents(base_path('deploy/vps/backup.sh'));

        $this->assertStringContainsString('--single-transaction', $backup);
        $this->assertStringContainsString('Dump completed', $backup);
        $this->assertStringContainsString('SHA256SUMS', $backup);
        $this->assertStringContainsString('flock', $backup);
    }

    public function test_live_restore_requires_confirmation_and_safety_backup(): void
    {
        $restore = (string) file_get_contents(base_path('deploy/vps/restore.sh'));

        $this->assertStringContainsString('Type the domain name to continue', $restore);
        $this->assertStringContainsString('deploy/vps/backup.sh', $restore);
        $this->assertStringContainsString('artisan down', $restore);
        $this->assertStringContainsString('trap bring_up EXIT', $restore);
    }

    public function test_production_compose_rotates_logs(): void
    {
        $yaml = (string) file_get_contents(base_path('docker-compose.production.yml'));

        $this->assertStringContainsString('LOG_STACK: ${LOG_STACK:-daily}', $yaml);
        $this->assertStringContainsString('max-size: "10m"', $yaml);
        $this->assertSame(3, substr_count($yaml, 'logging: *capped-logging'));
    }

    public function test_ops_env_template_is_documented_and_gitignored(): void
    {
        $example = (string) file_get_contents(base_path('deploy/vps/env.ops.example'));

        foreach ([
            'BACKUP_DIR=',
            'BACKUP_RETENTION_DAYS=',
            'BACKUP_RCLONE_REMOTE=',
            'BACKUP_HEALTHCHECK_URL=',
            'MONITOR_HEALTHCHECK_URL=',
            'MONITOR_DISK_MAX_PERCENT=',
            'MONITOR_TLS_MIN_DAYS=',
            'MONITOR_BACKUP_MAX_AGE_HOURS=',
            'MONITOR_MAX_DAILY_ERRORS=',
        ] as $key) {
            $this->assertStringContainsString($key, $example);
        }

        $gitignore = (string) file_get_contents(base_path('.gitignore'));
        $this->assertMatchesRegularExpression('/^\/?\.env\.ops$/m', $gitignore);
    }

    public function test_mac_pull_script_verifies_and_skips_unfinished_backups(): void
    {
        $path = base_path('deploy/mac/pull-backups.sh');

        $this->assertFileExists($path);
        $this->assertTrue(is_executable($path));

        $process = new Process(['bash', '-n', $path]);
        $process->run();
        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());

        $script = (string) file_get_contents($path);
        $this->assertStringContainsString("--exclude='*.partial'", $script);
        $this->assertStringContainsString('shasum -a 256 -c SHA256SUMS', $script);
        $this->assertStringContainsString('launchctl bootstrap', $script);

        $this->assertFileExists(base_path('deploy/mac/env.backup-pull.example'));
        $this->assertMatchesRegularExpression('/^\/?\.env\.backup-pull$/m', (string) file_get_contents(base_path('.gitignore')));
    }

    public function test_runbook_documents_step_48_operations(): void
    {
        $runbook = (string) file_get_contents(base_path('deploy/vps/RUNBOOK.md'));

        $this->assertStringContainsString('## 8. Operations', $runbook);
        $this->assertStringContainsString('restore.sh --test', $runbook);
        $this->assertStringContainsString('monitor.sh', $runbook);
        $this->assertStringContainsString('crontab -e', $runbook);
        $this->assertStringContainsString('pull-backups.sh --install', $runbook);
    }
}
