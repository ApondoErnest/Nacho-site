<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Step 47 — VPS deploy pack (see deploy/vps/RUNBOOK.md).
 */
class Step47VpsDeployConfigTest extends TestCase
{
    public function test_vps_deploy_files_exist(): void
    {
        $root = base_path();

        $this->assertFileExists("{$root}/docker-compose.production.yml");
        $this->assertFileExists("{$root}/deploy/vps/RUNBOOK.md");
        $this->assertFileExists("{$root}/deploy/vps/deploy.sh");
        $this->assertFileExists("{$root}/deploy/vps/env.production.example");
        $this->assertFileExists("{$root}/deploy/vps/nginx-host/noblevehicletestingcompany.com.conf");
        $this->assertFileExists("{$root}/deploy/vps/nginx-host/noblevehicletestingcompany.com.ssl.conf");
    }

    public function test_production_compose_binds_docker_nginx_to_localhost_8083(): void
    {
        $yaml = file_get_contents(base_path('docker-compose.production.yml'));

        $this->assertIsString($yaml);
        $this->assertStringContainsString('127.0.0.1:8083:80', $yaml);
        $this->assertStringContainsString('APP_ENV: production', $yaml);
        $this->assertStringContainsString('APP_DEBUG: "false"', $yaml);
    }

    public function test_host_nginx_ssl_config_proxies_to_8083_and_redirects_www(): void
    {
        $nginx = file_get_contents(base_path('deploy/vps/nginx-host/noblevehicletestingcompany.com.ssl.conf'));

        $this->assertIsString($nginx);
        $this->assertStringContainsString('proxy_pass http://127.0.0.1:8083', $nginx);
        $this->assertStringContainsString('server_name www.noblevehicletestingcompany.com', $nginx);
        $this->assertStringContainsString('return 301 https://noblevehicletestingcompany.com', $nginx);
    }

    public function test_env_production_example_uses_canonical_app_url(): void
    {
        $env = file_get_contents(base_path('deploy/vps/env.production.example'));

        $this->assertIsString($env);
        $this->assertStringContainsString('APP_URL=https://noblevehicletestingcompany.com', $env);
        $this->assertStringContainsString('APP_DEBUG=false', $env);
    }
}
