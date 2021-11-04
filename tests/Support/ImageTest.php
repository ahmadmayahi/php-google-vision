<?php

namespace AhmadMayahi\Vision\Tests\Support;

use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Tests\TestCase;
use GdImage;

final class ImageTest extends TestCase
{
    /** @test */
    public function it_should_create_image(): void
    {
        $image = new Image($this->getFile());

        $this->assertInstanceOf(GdImage::class, $image->getImage());
    }

    /** @test */
    public function it_should_get_image_dimensions()
    {
        $image = new Image($this->getFile());

        $this->assertEquals(340, $image->getHeight());
        $this->assertEquals(650, $image->getWidth());
    }
}
