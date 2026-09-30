<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Step 46 — Docker stack configuration (see docs/DEPLOYMENT.md §2).
 */
class Step46DockerConfigTest extends TestCase
{
    public function test_docker_stack_files_exist(): void
    {
        $root = base_path();

        $this->assertFileExists("{$root}/Dockerfile");
        $this->assertFileExists("{$root}/docker-compose.yml");
        $this->assertFileExists("{$root}/.dockerignore");
        $this->assertFileExists("{$root}/.env.docker.example");
        $this->assertFileExists("{$root}/docker/nginx/default.conf");
        $this->assertFileExists("{$root}/docker/entrypoint.sh");
        $this->assertFileExists("{$root}/docker/php/php.ini");
    }

    public function test_compose_declares_app_nginx_and_mysql_services(): void
    {
        $compose = file_get_contents(base_path('docker-compose.yml'));

        $this->assertIsString($compose);
        $this->assertMatchesRegularExpression('/^\s*app:/m', $compose);
        $this->assertMatchesRegularExpression('/^\s*nginx:/m', $compose);
        $this->assertMatchesRegularExpression('/^\s*mysql:/m', $compose);
        $this->assertStringContainsString('target: app', $compose);
        $this->assertStringContainsString('target: web', $compose);
        $this->assertStringContainsString('DOCKER_HTTP_PORT', $compose);
    }

    public function test_nginx_routes_php_to_app_fpm(): void
    {
        $nginx = file_get_contents(base_path('docker/nginx/default.conf'));

        $this->assertIsString($nginx);
        $this->assertStringContainsString('fastcgi_pass app:9000', $nginx);
        $this->assertStringContainsString('/var/www/html/public', $nginx);
    }

    public function test_dockerfile_builds_frontend_app_and_web_targets(): void
    {
        $dockerfile = file_get_contents(base_path('Dockerfile'));

        $this->assertIsString($dockerfile);
        $this->assertStringContainsString('FROM node:', $dockerfile);
        $this->assertStringContainsString('FROM php:8.4-fpm', $dockerfile);
        $this->assertStringContainsString('AS app', $dockerfile);
        $this->assertStringContainsString('AS web', $dockerfile);
        $this->assertStringContainsString('npm run build', $dockerfile);
    }

    #[Group('docker')]
    public function test_docker_compose_config_is_valid_when_cli_available(): void
    {
        if (! $this->commandExists('docker')) {
            $this->markTestSkipped('Docker CLI not available.');
        }

        $output = [];
        $exitCode = 0;
        exec('docker compose -f '.escapeshellarg(base_path('docker-compose.yml')).' config 2>&1', $output, $exitCode);

        $this->assertSame(0, $exitCode, implode("\n", $output));
        $combined = implode("\n", $output);
        $this->assertMatchesRegularExpression('/^\s*app:/m', $combined);
        $this->assertMatchesRegularExpression('/^\s*nginx:/m', $combined);
        $this->assertMatchesRegularExpression('/^\s*mysql:/m', $combined);
        $this->assertStringContainsString('image: mysql:8.4', $combined);
        $this->assertStringContainsString('target: app', $combined);
        $this->assertStringContainsString('target: web', $combined);
    }

    private function commandExists(string $command): bool
    {
        $path = trim((string) shell_exec(sprintf('command -v %s 2>/dev/null', escapeshellarg($command))));

        return $path !== '';
    }
}
