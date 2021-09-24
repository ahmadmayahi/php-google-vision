<?php

namespace AhmadMayahi\GoogleVision\Tests\Detectors;

use AhmadMayahi\GoogleVision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;

class LogoTest extends TestCase
{
    /** @test */
    public function it_should_get_logo_original_google_vision_response(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $repeatedField = $this->createMock(RepeatedField::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);

        $annotateImageResponse
            ->expects($this->once())
            ->method('getLogoAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('logoDetection')
            ->willReturn($annotateImageResponse);

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this
            ->getVision()
            ->logoDetection()
            ->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $response);
    }

    /** @test */
    public function it_should_get_logo_detection(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $repeatedField = $this->createMock(RepeatedField::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $repeatedFieldIter = $this->createMock(RepeatedFieldIter::class);

        $entityAnnotation = $this->createMock(EntityAnnotation::class);
        $entityAnnotation
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Google');

        $repeatedField
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->mockIterator($repeatedFieldIter, [$entityAnnotation]));


        $annotateImageResponse
            ->expects($this->once())
            ->method('getLogoAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('logoDetection')
            ->willReturn($annotateImageResponse);

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this
            ->getVision()
            ->logoDetection()
            ->detect();

        $this->assertIsArray($response);
        $this->assertCount(1, $response);
        $this->assertEquals('Google', $response[0]);
    }
}
