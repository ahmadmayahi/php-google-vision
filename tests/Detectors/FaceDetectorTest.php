<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\Face as FaceData;
use AhmadMayahi\Vision\Data\Vertex as VertexData;
use AhmadMayahi\Vision\Detectors\Face;
use AhmadMayahi\Vision\Enums\Color;
use AhmadMayahi\Vision\Enums\Likelihood;
use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\BoundingPoly;
use Google\Cloud\Vision\V1\FaceAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Vertex;
use Google\Protobuf\Internal\RepeatedField;

final class FaceDetectorTest extends TestCase
{
    /** @test */
    public function it_should_get_face_original_response(): void
    {
        $repeatedField = $this->createMock(RepeatedField::class);
        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotatorImageResponse
            ->expects($this->once())
            ->method('getFaceAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $imageAnnotatorClient
            ->expects($this->once())
            ->method('faceDetection')
            ->willReturn($annotatorImageResponse);

        $image = $this->createMock(Image::class);

        $face = new Face($imageAnnotatorClient, $this->getFile(), $image);

        $this->assertInstanceOf(RepeatedField::class, $face->getOriginalResponse());
    }

    /** @test */
    public function it_should_detect_faces_in_and_return_face_data_object(): void
    {
        [$face1, $face2] = $this->faces();

        $imageAnnotatorClient = $this->getMockedData();

        $image = $this->createMock(Image::class);

        $faceDetection = new Face($imageAnnotatorClient, $this->getFile(), $image);
        $faceDetectionResult = $faceDetection->asArray();

        $this->assertCount(2, $faceDetectionResult);
        $this->assertEquals([$face1, $face2], $faceDetectionResult);
    }

    /** @test */
    public function it_should_draw_box_around_faces(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);

        [$face1, $face2] = $this->faces();

        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotatorImageResponse
            ->expects($this->once())
            ->method('getFaceAnnotations')
            ->willReturn($this->createRepeatedFieldIter([$this->createFaceAnnotation($face1), $this->createFaceAnnotation($face2)]));

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('faceDetection')
            ->willReturn($annotatorImageResponse);

        $imageAnnotatorClient->expects($this->once())->method('close');

        $image = $this->createMock(Image::class);
        $image
            ->expects($this->exactly(2))
            ->method('drawRectangle')
            ->withConsecutive(
                [330, 24, 546, 276, Color::GREEN],
                [77, 23, 273, 250, Color::GREEN],
            );

        $face = new Face($imageAnnotatorClient, $this->getFile(), $image);
        $face->drawBoxAroundFaces()->toJpeg('out.jpg');
    }

    private function createFaceAnnotation(FaceData $faceData)
    {
        $faceAnnotation = $this->createMock(FaceAnnotation::class);
        $boundingPoly = $this->createMock(BoundingPoly::class);

        $faceAnnotation
            ->expects($this->once())
            ->method('getAngerLikelihood')
            ->willReturn(Likelihood::fromVal($faceData->anger));

        $faceAnnotation
            ->expects($this->once())
            ->method('getJoyLikelihood')
            ->willReturn(Likelihood::fromVal($faceData->joy));

        $faceAnnotation
            ->expects($this->once())
            ->method('getSurpriseLikelihood')
            ->willReturn(Likelihood::fromVal($faceData->surprise));

        $faceAnnotation
            ->expects($this->once())
            ->method('getBlurredLikelihood')
            ->willReturn(Likelihood::fromVal($faceData->blurred));

        $faceAnnotation
            ->expects($this->once())
            ->method('getHeadwearLikelihood')
            ->willReturn(Likelihood::fromVal($faceData->headwear));

        $faceAnnotation
            ->expects($this->once())
            ->method('getUnderExposedLikelihood')
            ->willReturn(Likelihood::fromVal($faceData->underExposed));

        $faceAnnotation
            ->expects($this->once())
            ->method('getDetectionConfidence')
            ->willReturn($faceData->detectionConfidence);

        $faceAnnotation
            ->expects($this->once())
            ->method('getLandmarkingConfidence')
            ->willReturn($faceData->landmarking);

        $vertices = [];

        /** @var VertexData $vertex */
        foreach ($faceData->bounds as $vertex) {
            $vertexMock = $this->createMock(Vertex::class);
            $vertexMock
                ->expects($this->once())
                ->method('getX')
                ->willReturn($vertex->x);

            $vertexMock
                ->expects($this->once())
                ->method('getY')
                ->willReturn($vertex->y);

            $vertices[] = $vertexMock;
        }

        $boundingPoly
            ->expects($this->once())
            ->method('getVertices')
            ->willReturn($this->createRepeatedFieldIter($vertices));

        $faceAnnotation
            ->expects($this->once())
            ->method('getBoundingPoly')
            ->willReturn($boundingPoly);

        return $faceAnnotation;
    }

    private function faces(): array
    {
        $face1 = new FaceData(
            anger: 'VERY_UNLIKELY',
            joy: 'VERY_LIKELY',
            surprise: 'VERY_UNLIKELY',
            blurred: 'VERY_UNLIKELY',
            headwear: 'VERY_UNLIKELY',
            underExposed: 'VERY_UNLIKELY',
            bounds: [
                new VertexData(330, 24),
                new VertexData(546, 26),
                new VertexData(546, 276),
                new VertexData(330, 276),
            ],
            detectionConfidence: 0.74070239067078,
            landmarking: 0.81482881307602,
        );

        $face2 = new FaceData(
            anger: 'VERY_UNLIKELY',
            joy: 'VERY_LIKELY',
            surprise: 'VERY_UNLIKELY',
            blurred: 'VERY_UNLIKELY',
            headwear: 'VERY_UNLIKELY',
            underExposed: 'VERY_UNLIKELY',
            bounds: [
                new VertexData(77, 23),
                new VertexData(273, 23),
                new VertexData(273, 250),
                new VertexData(77, 250),
            ],
            detectionConfidence: 0.44881856441498,
            landmarking: 0.40206038951874,
        );

        return [$face1, $face2];
    }

    public function getMockedData()
    {
        [$face1, $face2] = $this->faces();

        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotatorImageResponse
            ->expects($this->once())
            ->method('getFaceAnnotations')
            ->willReturn($this->createRepeatedFieldIter([$this->createFaceAnnotation($face1), $this->createFaceAnnotation($face2)]));

        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $imageAnnotatorClient
            ->expects($this->once())
            ->method('faceDetection')
            ->willReturn($annotatorImageResponse);
        $imageAnnotatorClient
            ->expects($this->once())
            ->method('close');

        return $imageAnnotatorClient;
    }
}
