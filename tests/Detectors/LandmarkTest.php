<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Detectors\Landmark;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\LocationInfo;
use Google\Protobuf\Internal\RepeatedField;
use Google\Type\LatLng;

final class LandmarkTest extends TestCase
{
    /** @test */
    public function it_should_get_landmark_original_response(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $repeatedField = $this->createMock(RepeatedField::class);

        $annotateImageResponse
            ->expects($this->once())
            ->method('getLandmarkAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('landmarkDetection')
            ->willReturn($annotateImageResponse);

        $response = (new Landmark($imageAnnotatorClient, $this->getFile()))->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $response);
    }

    /** @test */
    public function it_should_get_landmark_data(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);

        $latLng = $this->createMock(LatLng::class);
        $latLng
            ->expects($this->once())
            ->method('getLatitude')
            ->willReturn(37.811013);
        $latLng
            ->method('getLongitude')
            ->willReturn(-122.477801);

        $locationInfo = $this->createMock(LocationInfo::class);
        $locationInfo
            ->method('getLatLng')
            ->willReturn($latLng);

        $entityAnnotation = $this->createMock(EntityAnnotation::class);
        $entityAnnotation
            ->expects($this->once())
            ->method('getDescription')
            ->willReturn('Golden Gate Bridge');

        $locationsRepeatedField = $this->createMock(RepeatedField::class);
        $locationsRepeatedField
            ->method('getIterator')
            ->willReturn($this->createRepeatedFieldIter([$locationInfo]));

        $entityAnnotation
            ->expects($this->once())
            ->method('getLocations')
            ->willReturn($locationsRepeatedField);

        $landmarkAnnotationsRepeatedField = $this->createMock(RepeatedField::class);
        $landmarkAnnotationsRepeatedField
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->createRepeatedFieldIter([$entityAnnotation]));

        $annotateImageResponse
            ->expects($this->once())
            ->method('getLandmarkAnnotations')
            ->willReturn($landmarkAnnotationsRepeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('landmarkDetection')
            ->willReturn($annotateImageResponse);

        $response = (new Landmark($imageAnnotatorClient, $this->getFile()))->detect();
        $response = iterator_to_array($response);

        $this->assertIsArray($response);
        $this->assertCount(1, $response);
        $this->assertEquals('Golden Gate Bridge', $response[0]->name);

        $this->assertEquals(37.811013, $response[0]->locations[0]->latitude);
        $this->assertEquals(-122.477801, $response[0]->locations[0]->longitude);
    }
}
