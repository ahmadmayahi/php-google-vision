<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Detectors\ObjectLocalizer;
use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Protobuf\Internal\RepeatedField;

final class ObjectLocalizerTest extends TestCase
{
    /** @test */
    public function it_should_get_object_localizer_original_response(): void
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
            ->willReturn($annotateImageResponse);

        $drawBoxImage = $this->createMock(Image::class);

        $response = (new ObjectLocalizer($imageAnnotatorClient, $this->getFile(), $drawBoxImage))->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $response);
    }
}
