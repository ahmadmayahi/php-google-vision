<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\LocalizedObjectData;
use AhmadMayahi\Vision\Tests\TestCase;
use AhmadMayahi\Vision\Utils\Container;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\BoundingPoly;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\LocalizedObjectAnnotation;
use Google\Cloud\Vision\V1\NormalizedVertex;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;

class ObjectLocalizerTest extends TestCase
{
    /** @test */
    public function it_should_get_object_localizer_original_google_vision_response(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $repeatedField = $this->createMock(RepeatedField::class);

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getLocalizedObjectAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('objectLocalization')
            ->with($this->getFileContents())
            ->willReturn($annotateImageResponse);

        Container::getInstance()->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this
            ->getVision()
            ->objectLocalizer()
            ->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $response);
    }

    /** @test */
    public function it_should_draw_box_around_objects(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $localizedObjectAnnotation = $this->createMock(LocalizedObjectAnnotation::class);

        $normalizedVertex = $this->createMock(NormalizedVertex::class);
        $normalizedVertex
            ->expects($this->once())
            ->method('getX')
            ->willReturn('10');

        $normalizedVertex
            ->expects($this->once())
            ->method('getY')
            ->willReturn('10');

        $repeatedFieldPoly = $this->createMock(RepeatedField::class);
        $repeatedFieldIterPoly = $this->createMock(RepeatedFieldIter::class);
        $repeatedFieldPoly
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->mockIterator($repeatedFieldIterPoly, [$normalizedVertex]));

        $boundingPoly = $this->createMock(BoundingPoly::class);
        $boundingPoly
            ->expects($this->once())
            ->method('getNormalizedVertices')
            ->willReturn($repeatedFieldPoly);

        $localizedObjectAnnotation
            ->expects($this->once())
            ->method('getBoundingPoly')
            ->willReturn($boundingPoly);

        $localizedObjectAnnotation
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Bicycle wheel');

        $localizedObjectAnnotation
            ->expects($this->once())
            ->method('getMid')
            ->willReturn('/m/01bqk0');

        $localizedObjectAnnotation
            ->expects($this->once())
            ->method('getLanguageCode')
            ->willReturn('');

        $localizedObjectAnnotation
            ->expects($this->once())
            ->method('getScore')
            ->willReturn(0.89648587);

        $repeatedField = $this->createMock(RepeatedField::class);
        $repeatedFieldIter = $this->createMock(RepeatedFieldIter::class);
        $repeatedField
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->mockIterator($repeatedFieldIter, [$localizedObjectAnnotation]));

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getLocalizedObjectAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('objectLocalization')
            ->with($this->getFileContents())
            ->willReturn($annotateImageResponse);

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this
            ->getVision()
            ->objectLocalizer()
            ->detect();

        /** @var LocalizedObjectData $item */
        foreach ($response as $item) {
            $this->assertInstanceOf(LocalizedObjectData::class, $item);
            $this->assertEquals('Bicycle wheel', $item->getName());
            $this->assertEquals('/m/01bqk0', $item->getMid());
        }
    }
}