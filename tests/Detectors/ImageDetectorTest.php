<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Protobuf\Internal\RepeatedField;

class ImageDetectorTest extends TestCase
{
    /** @test */
    public function it_should_get_image_original_google_vision_response(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $repeatedField = $this->createMock(RepeatedField::class);

        $annotatorImageResponse
            ->expects($this->once())
            ->method('getTextAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('documentTextDetection')
            ->willReturn($annotatorImageResponse);

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $vision = $this
            ->getVision()
            ->imageTextDetection()
            ->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $vision);
    }
}
