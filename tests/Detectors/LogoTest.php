<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\LogoData;
use AhmadMayahi\Vision\Detectors\Logo;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;

final class LogoTest extends TestCase
{
    /** @test */
    public function it_should_get_logo_original_response(): void
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

        $response = (new Logo($imageAnnotatorClient, $this->getFile()))->getOriginalResponse();

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

        $logoDetector = (new Logo($imageAnnotatorClient, $this->getFile()))->detect();

        /** @var LogoData[] $response */
        $response = iterator_to_array($logoDetector);

        $this->assertIsArray($response);
        $this->assertCount(1, $response);
        $this->assertEquals('Google', $response[0]->description);
    }
}
