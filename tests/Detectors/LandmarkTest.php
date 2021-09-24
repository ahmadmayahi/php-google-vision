<?php

namespace AhmadMayahi\GoogleVision\Tests\Detectors;

use AhmadMayahi\GoogleVision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\LocationInfo;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;
use Google\Type\LatLng;

class LandmarkTest extends TestCase
{
    /** @test */
    public function it_should_get_landmark_original_google_vision_response(): void
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

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this->getVision()->landmarkDetection()->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $response);
    }

    /** @test */
    public function it_should_get_landmark_analyzer(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $repeatedFieldIter = $this->createMock(RepeatedFieldIter::class);

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
            ->willReturn($this->mockIterator($repeatedFieldIter, [$locationInfo]));

        $entityAnnotation
            ->expects($this->once())
            ->method('getLocations')
            ->willReturn($locationsRepeatedField);

        $landmarkAnnotationsRepeatedFieldIter = $this->createMock(RepeatedFieldIter::class);
        $landmarkAnnotationsRepeatedField = $this->createMock(RepeatedField::class);
        $landmarkAnnotationsRepeatedField
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->mockIterator($landmarkAnnotationsRepeatedFieldIter, [$entityAnnotation]));

        $annotateImageResponse
            ->expects($this->once())
            ->method('getLandmarkAnnotations')
            ->willReturn($landmarkAnnotationsRepeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('landmarkDetection')
            ->willReturn($annotateImageResponse);

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this
            ->getVision()
            ->landmarkDetection()
            ->detect();

        $this->assertIsArray($response);
        $this->assertCount(1, $response);
        $this->assertEquals('Golden Gate Bridge', $response[0]->getName());
        $this->assertEquals(37.811013, $response[0]->getLocations()[0]['latitude']);
        $this->assertEquals(-122.477801, $response[0]->getLocations()[0]['longitude']);
    }
}
