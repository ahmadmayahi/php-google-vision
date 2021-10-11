<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\FaceData;
use AhmadMayahi\Vision\Data\VertexData;
use AhmadMayahi\Vision\Detectors\Face;
use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\BoundingPoly;
use Google\Cloud\Vision\V1\FaceAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\V1\Vertex;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\RepeatedFieldIter;

final class FaceDetectorTest extends TestCase
{
    /** @test */
    public function it_should_get_face_original_response(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $repeatedField = $this->createMock(RepeatedField::class);

        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotatorImageResponse
            ->expects($this->once())
            ->method('getFaceAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('faceDetection')
            ->willReturn($annotatorImageResponse);

        $image = $this->createMock(Image::class);

        $face = new Face($imageAnnotatorClient, $this->getFile(), $image);

        $this->assertInstanceOf(RepeatedField::class, $face->getOriginalResponse());
    }

    /** @test */
    public function it_should_get_face_data(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $faceAnnotation = $this->createMock(FaceAnnotation::class);
        $boundingPoly = $this->createMock(BoundingPoly::class);

        $vertex1 = $this->createMock(Vertex::class);
        $vertex1
            ->expects($this->once())
            ->method('getX')
            ->willReturn(330);

        $vertex1
            ->expects($this->once())
            ->method('getY')
            ->willReturn(24);

        $faceAnnotation
            ->expects($this->once())
            ->method('getAngerLikelihood')
            ->willReturn(Likelihood::VERY_UNLIKELY);

        $faceAnnotation
            ->expects($this->once())
            ->method('getJoyLikelihood')
            ->willReturn(Likelihood::VERY_LIKELY);

        $faceAnnotation
            ->expects($this->once())
            ->method('getSurpriseLikelihood')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getBlurredLikelihood')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getHeadwearLikelihood')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getUnderExposedLikelihood')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getDetectionConfidence')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getLandmarkingConfidence')
            ->willReturn(Likelihood::POSSIBLE);

        $boundingPoly
            ->expects($this->once())
            ->method('getVertices')
            ->willReturn($this->createIterator([$vertex1]));

        $faceAnnotation
            ->expects($this->once())
            ->method('getBoundingPoly')
            ->willReturn($boundingPoly);

        $repeatedFieldIter = $this->createMock(RepeatedFieldIter::class);

        $repeatedField = $this->createMock(RepeatedField::class);
        $repeatedField
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->mockIterator($repeatedFieldIter, [$faceAnnotation]));

        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotatorImageResponse
            ->expects($this->once())
            ->method('getFaceAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('faceDetection')
            ->willReturn($annotatorImageResponse);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('close');

        $image = $this->createMock(Image::class);

        $face = new Face($imageAnnotatorClient, $this->getFile(), $image);
        $faces = $face->asArray();

        $this->assertCount(1, $faces);
        $this->assertInstanceOf(FaceData::class, $faces[0]);
        $this->assertEquals('VERY_UNLIKELY', $faces[0]->anger);
        $this->assertEquals('VERY_LIKELY', $faces[0]->joy);
        $this->assertEquals('POSSIBLE', $faces[0]->surprise);
        $this->assertEquals([new VertexData(330, 24)], $faces[0]->bounds);
    }

    /** @test */
    public function it_should_draw_box_around_faces()
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $repeatedField = $this->createMock(RepeatedField::class);
        $faceAnnotation = $this->createMock(FaceAnnotation::class);
        $boundingPoly = $this->createMock(BoundingPoly::class);

        $vertex = $this->createMock(Vertex::class);
        $vertex
            ->expects($this->exactly(4))
            ->method('getX')
            ->willReturnOnConsecutiveCalls(89, 50, 109, 98);

        $vertex
            ->expects($this->exactly(4))
            ->method('getY')
            ->willReturnOnConsecutiveCalls(74, 68, 209, 110);

        $iter = $this->mockIterator($this->createMock(RepeatedFieldIter::class), [$vertex, $vertex, $vertex, $vertex]);

        $boundingPoly
            ->expects($this->once())
            ->method('getVertices')
            ->willReturn($iter);

        $faceAnnotation
            ->expects($this->once())
            ->method('getAngerLikelihood')
            ->willReturn(Likelihood::VERY_UNLIKELY);

        $faceAnnotation
            ->expects($this->once())
            ->method('getJoyLikelihood')
            ->willReturn(Likelihood::VERY_LIKELY);

        $faceAnnotation
            ->expects($this->once())
            ->method('getSurpriseLikelihood')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getBlurredLikelihood')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getHeadwearLikelihood')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getUnderExposedLikelihood')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getDetectionConfidence')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getLandmarkingConfidence')
            ->willReturn(Likelihood::POSSIBLE);

        $faceAnnotation
            ->expects($this->once())
            ->method('getBoundingPoly')
            ->willReturn($boundingPoly);

        $repeatedFieldIter = $this->createMock(RepeatedFieldIter::class);

        $repeatedField
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->mockIterator($repeatedFieldIter, [$faceAnnotation]));

        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotatorImageResponse
            ->expects($this->once())
            ->method('getFaceAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('faceDetection')
            ->willReturn($annotatorImageResponse);

        $imageAnnotatorClient->expects($this->once())->method('close');

        $image = $this->createMock(Image::class);
        $image
            ->expects($this->once())
            ->method('drawRectangle')
            ->with(89, 74, 109, 209);

        $face = new Face($imageAnnotatorClient, $this->getFile(), $image);
        $face->drawBoxAroundFaces()->toJpeg('out.jpg');
    }
}
