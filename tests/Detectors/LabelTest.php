<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;

class LabelTest extends TestCase
{
    /** @test */
    public function it_should_get_label_original_google_vision_response(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $repeatedField = $this->createMock(RepeatedField::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);

        $annotateImageResponse
            ->expects($this->once())
            ->method('getLabelAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('labelDetection')
            ->willReturn($annotateImageResponse);

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this
            ->getVision()
            ->labelDetection()
            ->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $response);
    }

    /** @test */
    public function it_should_get_label_detection(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $repeatedField = $this->createMock(RepeatedField::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $repeatedFieldIter = $this->createMock(RepeatedFieldIter::class);

        $entityAnnotation = $this->createMock(EntityAnnotation::class);
        $entityAnnotation
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Wall');

        $repeatedField
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->mockIterator($repeatedFieldIter, [$entityAnnotation]));


        $annotateImageResponse
            ->expects($this->once())
            ->method('getLabelAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('labelDetection')
            ->willReturn($annotateImageResponse);

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this
            ->getVision()
            ->labelDetection()
            ->detect();

        $this->assertIsArray($response);
        $this->assertCount(1, $response);
        $this->assertEquals('Wall', $response[0]);
    }
}
