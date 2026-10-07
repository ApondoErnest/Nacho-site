<?php

namespace Tests\Unit;

use App\Support\PublicImage;
use Tests\TestCase;

class PublicImageTest extends TestCase
{
    public function test_sources_falls_back_to_webp_when_original_is_missing(): void
    {
        $sources = PublicImage::sources('images/contact/yaounde-1.png');

        $this->assertNotNull($sources);
        $this->assertStringContainsString('yaounde-1', $sources['src']);
    }
}
