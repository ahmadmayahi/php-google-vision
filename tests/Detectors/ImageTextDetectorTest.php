<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Detectors\ImageText;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Protobuf\Internal\RepeatedField;

final class ImageTextDetectorTest extends TestCase
{
    /** @test */
    public function it_should_get_image_original_response(): void
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

        $image = new ImageText(
            $imageAnnotatorClient,
            $this->getFile(),
        );

        $this->assertInstanceOf(RepeatedField::class, $image->getOriginalResponse());
    }

    /** @test */
    public function it_should_get_plain_text(): void
    {
        $entityAnnotation = $this->createMock(EntityAnnotation::class);
        $entityAnnotation
            ->expects($this->once())
            ->method('getLocale')
            ->willReturn('en');
        $entityAnnotation
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Hello World');

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getTextAnnotations')
            ->willReturn($this->createRepeatedField([$entityAnnotation]));

        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $imageAnnotatorClient
            ->expects($this->once())
            ->method('textDetection')
            ->willReturn($annotateImageResponse);

        $image = new ImageText(
            $imageAnnotatorClient,
            $this->getFile(),
        );

        $plain = $image->plain();

        $this->assertEquals('en', $plain->locale);
        $this->assertEquals('Hello World', $plain->text);
    }

    /** @test */
    public function it_should_get_document_text(): void
    {
        $entityAnnotation = $this->createMock(EntityAnnotation::class);
        $entityAnnotation
            ->expects($this->once())
            ->method('getLocale')
            ->willReturn('en');
        $entityAnnotation
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Hello World');

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getTextAnnotations')
            ->willReturn($this->createRepeatedField([$entityAnnotation]));

        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $imageAnnotatorClient
            ->expects($this->once())
            ->method('documentTextDetection')
            ->willReturn($annotateImageResponse);

        $image = new ImageText(
            $imageAnnotatorClient,
            $this->getFile(),
        );

        $plain = $image->document();

        $this->assertEquals('en', $plain->locale);
        $this->assertEquals('Hello World', $plain->text);
    }
}
