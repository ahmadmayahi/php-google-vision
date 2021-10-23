<?php

namespace AhmadMayahi\Vision\Tests;

use AhmadMayahi\Vision\Detectors\CropHints;
use AhmadMayahi\Vision\Detectors\Face;
use AhmadMayahi\Vision\Detectors\ImageProperties;
use AhmadMayahi\Vision\Detectors\ImageText;
use AhmadMayahi\Vision\Detectors\Label;
use AhmadMayahi\Vision\Detectors\Landmark;
use AhmadMayahi\Vision\Detectors\Logo;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer;
use AhmadMayahi\Vision\Detectors\SafeSearch;
use AhmadMayahi\Vision\Detectors\Web;
use AhmadMayahi\Vision\Vision;

final class VisionTest extends TestCase
{
    /** @test */
    public function it_should_return_singleton_upon_calling_init_method()
    {
        $obj1 = Vision::init($this->getConfig(), $this->getImageAnnotateClient());
        $obj2 = Vision::init($this->getConfig(), $this->getImageAnnotateClient());

        $this->assertTrue(spl_object_hash($obj1) === spl_object_hash($obj2));
    }

    /** @test */
    public function it_should_return_crop_hints_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->cropHintsDetection();

        $this->assertInstanceOf(CropHints::class, $vision);
    }

    /** @test */
    public function it_should_return_face_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->faceDetection();

        $this->assertInstanceOf(Face::class, $vision);
    }

    /** @test */
    public function it_should_return_image_text_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->imageTextDetection();

        $this->assertInstanceOf(ImageText::class, $vision);
    }

    /** @test */
    public function it_should_return_image_properties_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->imagePropertiesDetection();

        $this->assertInstanceOf(ImageProperties::class, $vision);
    }

    /** @test */
    public function it_should_return_label_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->labelDetection();

        $this->assertInstanceOf(Label::class, $vision);
    }

    /** @test */
    public function it_should_return_landmark_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->landmarkDetection();

        $this->assertInstanceOf(Landmark::class, $vision);
    }

    /** @test */
    public function it_should_return_logo_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->logoDetection();

        $this->assertInstanceOf(Logo::class, $vision);
    }

    /** @test */
    public function it_should_return_object_localizer_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->objectLocalizer();

        $this->assertInstanceOf(ObjectLocalizer::class, $vision);
    }

    /** @test */
    public function it_should_return_safe_search_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->safeSearchDetection();

        $this->assertInstanceOf(SafeSearch::class, $vision);
    }

    /** @test */
    public function it_should_return_safe_web_detector(): void
    {
        $vision = Vision::init($this->getConfig(), $this->getImageAnnotateClient())
            ->file($this->getFilePathname())
            ->webDetection();

        $this->assertInstanceOf(Web::class, $vision);
    }
}
