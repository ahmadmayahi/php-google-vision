<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\ImageProperties as ImagePropertiesData;
use AhmadMayahi\Vision\Detectors\ImageProperties;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\ColorInfo;
use Google\Cloud\Vision\V1\DominantColorsAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\ImageProperties as GoogleVisionImageProperties;
use Google\Type\Color;

final class ImagePropertiesTest extends TestCase
{
    /** @test */
    public function it_should_get_image_properties_original_response()
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $imageProperties = $this->createMock(GoogleVisionImageProperties::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);

        $annotateImageResponse
           ->expects($this->once())
           ->method('getImagePropertiesAnnotation')
           ->willReturn($imageProperties);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('imagePropertiesDetection')
            ->willReturn($annotateImageResponse);

        $imageProperties = new ImageProperties(
            $imageAnnotatorClient,
            $this->getFile()
        );

        $response = $imageProperties->getOriginalResponse();

        $this->assertInstanceOf(GoogleVisionImageProperties::class, $response);
    }

    /** @test */
    public function it_should_get_image_properties_data()
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $imageProperties = $this->createMock(GoogleVisionImageProperties::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $dominantColors = $this->createMock(DominantColorsAnnotation::class);

        $color = $this->createMock(Color::class);
        $color->expects($this->once())->method('getRed')->willReturn(243.0);
        $color->expects($this->once())->method('getGreen')->willReturn(241.0);
        $color->expects($this->once())->method('getBlue')->willReturn(240.0);

        $colorInfo = $this->createMock(ColorInfo::class);
        $colorInfo
            ->expects($this->once())
            ->method('getPixelFraction')
            ->willReturn(0.11830238997936);

        $colorInfo
            ->expects($this->once())
            ->method('getColor')
            ->willReturnOnConsecutiveCalls($color);

        $dominantColors
            ->expects($this->once())
            ->method('getColors')
            ->willReturn($this->createRepeatedFieldIter([$colorInfo]));

        $imageProperties
            ->expects($this->once())
            ->method('getDominantColors')
            ->willReturn($dominantColors);

        $annotateImageResponse
            ->expects($this->once())
            ->method('getImagePropertiesAnnotation')
            ->willReturn($imageProperties);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('imagePropertiesDetection')
            ->willReturn($annotateImageResponse);

        $imageProperties = new ImageProperties(
            $imageAnnotatorClient,
            $this->getFile(),
        );

        $response = iterator_to_array($imageProperties->detect());

        $this->assertCount(1, $response);
        $this->assertIsArray($response);
        $this->assertInstanceOf(ImagePropertiesData::class, $response[0]);
    }
}
