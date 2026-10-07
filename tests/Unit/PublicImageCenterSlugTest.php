<?php

namespace Tests\Unit;

use App\Support\PublicImage;
use Tests\TestCase;

class PublicImageCenterSlugTest extends TestCase
{
    public function test_expansion_image_resolves_for_novetesco_slug(): void
    {
        $url = PublicImage::centerImageUrl('novetesco-douala', null, null, 'expansion');

        $this->assertNotNull($url);
        $this->assertStringContainsString('douala', $url);
    }

    public function test_expansion_image_falls_back_to_featured_image(): void
    {
        $url = PublicImage::centerImageUrl(
            'novetesco-kumba',
            'images/homepage/kumba-coming-soon.png',
            null,
            'expansion',
        );

        $this->assertNotNull($url);
        $this->assertStringContainsString('kumba', $url);
    }
}
