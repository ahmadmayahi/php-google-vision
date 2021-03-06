<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Detectors\Label;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Protobuf\Internal\RepeatedField;

final class LabelTest extends TestCase
{
    /** @test */
    public function it_should_get_label_original_response(): void
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

        $response = (new Label($imageAnnotatorClient, $this->getFile()))->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $response);
    }

    /** @test */
    public function it_should_get_label_data(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);

        $entityAnnotation = $this->createMock(EntityAnnotation::class);
        $entityAnnotation
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Wall');

        $annotateImageResponse
            ->expects($this->once())
            ->method('getLabelAnnotations')
            ->willReturn($this->createRepeatedFieldIter([$entityAnnotation]));

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('labelDetection')
            ->willReturn($annotateImageResponse);

        $response = (new Label($imageAnnotatorClient, $this->getFile()))->detect();
        $response = iterator_to_array($response);

        $this->assertIsArray($response);
        $this->assertCount(1, $response);
        $this->assertEquals('Wall', $response[0]->description);
    }
}
