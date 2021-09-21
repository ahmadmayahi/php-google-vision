<?php

namespace AhmadMayahi\GoogleVision\Tests\Detectors;

use AhmadMayahi\GoogleVision\Data\SafeSearchData;
use AhmadMayahi\GoogleVision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\V1\SafeSearchAnnotation;

class SafeSearchTest extends TestCase
{
    /** @test */
    public function it_should_get_safe_search_original_google_vision_response(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $safeSearchAnnotation = $this->createMock(SafeSearchAnnotation::class);

        $annotatorImageResponse
            ->expects($this->once())
            ->method('getSafeSearchAnnotation')
            ->willReturn($safeSearchAnnotation);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('safeSearchDetection')
            ->with($this->getFileContents())
            ->willReturn($annotatorImageResponse);

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this
            ->getVision()
            ->safeSearchDetection()
            ->getOriginalResponse();

        $this->assertInstanceOf(SafeSearchAnnotation::class, $response);
    }

    /** @test */
    public function it_should_get_analyzer(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $safeSearchAnootation = $this->createMock(SafeSearchAnnotation::class);

        $safeSearchAnootation
            ->expects($this->once())
            ->method('getAdult')
            ->willReturn(Likelihood::VERY_UNLIKELY);

        $safeSearchAnootation
            ->expects($this->once())
            ->method('getMedical')
            ->willReturn(Likelihood::POSSIBLE);

        $safeSearchAnootation
            ->expects($this->once())
            ->method('getViolence')
            ->willReturn(Likelihood::UNKNOWN);

        $safeSearchAnootation
            ->expects($this->once())
            ->method('getRacy')
            ->willReturn(Likelihood::VERY_LIKELY);

        $safeSearchAnootation
            ->expects($this->once())
            ->method('getSpoof')
            ->willReturn(Likelihood::UNLIKELY);

        $annotatorImageResponse
            ->expects($this->once())
            ->method('getSafeSearchAnnotation')
            ->willReturn($safeSearchAnootation);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('safeSearchDetection')
            ->with($this->getFileContents())
            ->willReturn($annotatorImageResponse);

        $this->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $response = $this
            ->getVision()
            ->safeSearchDetection()
            ->detect();

        $this->assertInstanceOf(SafeSearchData::class, $response);
    }
}
