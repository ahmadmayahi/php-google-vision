<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\SafeSearch as SafeSearchData;
use AhmadMayahi\Vision\Detectors\SafeSearch;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\V1\SafeSearchAnnotation;

final class SafeSearchTest extends TestCase
{
    /** @test */
    public function it_should_get_safe_search_original_response(): void
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
            ->willReturn($annotatorImageResponse);

        $response = (new SafeSearch($imageAnnotatorClient, $this->getFile()))->getOriginalResponse();

        $this->assertInstanceOf(SafeSearchAnnotation::class, $response);
    }

    /** @test */
    public function it_should_get_safe_search_data(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $safeSearchAnnotation = $this->createMock(SafeSearchAnnotation::class);

        $safeSearchAnnotation
            ->expects($this->once())
            ->method('getAdult')
            ->willReturn(Likelihood::VERY_UNLIKELY);

        $safeSearchAnnotation
            ->expects($this->once())
            ->method('getMedical')
            ->willReturn(Likelihood::POSSIBLE);

        $safeSearchAnnotation
            ->expects($this->once())
            ->method('getViolence')
            ->willReturn(Likelihood::UNKNOWN);

        $safeSearchAnnotation
            ->expects($this->once())
            ->method('getRacy')
            ->willReturn(Likelihood::VERY_LIKELY);

        $safeSearchAnnotation
            ->expects($this->once())
            ->method('getSpoof')
            ->willReturn(Likelihood::UNLIKELY);

        $annotatorImageResponse
            ->expects($this->once())
            ->method('getSafeSearchAnnotation')
            ->willReturn($safeSearchAnnotation);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('safeSearchDetection')
            ->willReturn($annotatorImageResponse);

        $response = (new SafeSearch($imageAnnotatorClient, $this->getFile()))->detect();

        $this->assertInstanceOf(SafeSearchData::class, $response);
    }
}
