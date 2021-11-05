<?php

namespace AhmadMayahi\Vision\Tests\Support;

use AhmadMayahi\Vision\Enums\Color;
use AhmadMayahi\Vision\Enums\Font;
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
    public function it_should_get_image_dimensions(): void
    {
        $image = new Image($this->getFile());

        $this->assertEquals(340, $image->getHeight());
        $this->assertEquals(650, $image->getWidth());
    }

    /** @test */
    public function it_should_draw_rectangle(): void
    {
        $image = new Image($this->getFile());

        $exportedImagePath = $this->getTempDir('rectangle.jpg');

        $image->drawRectangle(10, 20, 30, 100, Color::GREEN);

        $image->toJpeg($exportedImagePath);

        $this->assertFileExists($exportedImagePath);

        $this->assertFileNotEquals($exportedImagePath, $this->getFilePathname());
    }

    /** @test */
    public function it_should_crop_image(): void
    {
        $image = new Image($this->getFile());

        $exportedImagePath = $this->getTempDir('crop.jpg');

        $image->cropImage(10, 20, 50, 50);

        $image->toJpeg($exportedImagePath);

        $this->assertFileExists($exportedImagePath);

        $this->assertFileNotEquals($exportedImagePath, $this->getFilePathname());
    }

    /** @test */
    public function it_should_draw_polygon(): void
    {
        $image = new Image($this->getFile());

        $exportedImagePath = $this->getTempDir('polygon.jpg');

        $image->drawPolygon([
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
        ], 4, Color::GREEN);

        $image->toJpeg($exportedImagePath);

        $this->assertFileExists($exportedImagePath);

        $this->assertFileNotEquals($exportedImagePath, $this->getFilePathname());
    }

    /** @test */
    public function it_should_write_text(): void
    {
        $image = new Image($this->getFile());

        $exportedImagePath = $this->getTempDir('text.jpg');

        $image->writeText('Hello World', Font::OPEN_SANS_MEDIUM, Color::MAGENTA, 15, 10, 10);

        $image->toJpeg($exportedImagePath);

        $this->assertFileExists($exportedImagePath);

        $this->assertFileNotEquals($exportedImagePath, $this->getFilePathname());
    }

    /** @test */
    public function it_should_export_to_png(): void
    {
        $image = new Image($this->getFile());

        $imgPath = $this->getTempDir('to_png.png');

        $image->toPng($imgPath);

        $this->assertEquals('image/png', mime_content_type($imgPath));
    }

    /** @test */
    public function it_should_export_to_jpeg(): void
    {
        $image = new Image($this->getFile());

        $imgPath = $this->getTempDir('to_jpg.jpg');

        $image->toJpeg($imgPath);

        $this->assertEquals('image/jpeg', mime_content_type($imgPath));
    }

    /** @test */
    public function it_should_export_to_bmp(): void
    {
        $image = new Image($this->getFile());

        $imgPath = $this->getTempDir('to_bmp.bmp');

        $image->toBmp($imgPath);

        $this->assertEquals('image/x-ms-bmp', mime_content_type($imgPath));
    }

    public function it_should_export_to_gif(): void
    {
        $image = new Image($this->getFile());

        $imgPath = $this->getTempDir('to_gif.gif');

        $image->toGif($imgPath);

        $this->assertEquals('image/git', mime_content_type($imgPath));

    }
}
