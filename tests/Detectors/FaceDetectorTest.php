<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\FaceData;
use AhmadMayahi\Vision\Data\VertexData;
use AhmadMayahi\Vision\Detectors\Face;
use AhmadMayahi\Vision\Tests\TestCase;
use AhmadMayahi\Vision\Utils\Image;
use Generator;
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
        $repeatedField = $this->createMock(RepeatedField::class);
        $faceAnnotation = $this->createMock(FaceAnnotation::class);
        $boundingPoly = $this->createMock(BoundingPoly::class);

        $vertex = $this->createMock(Vertex::class);
        $vertex
            ->expects($this->once())
            ->method('getX')
            ->willReturn(89.99);
        $vertex
            ->expects($this->once())
            ->method('getY')
            ->willReturn(74.22);

        $boundingPoly
            ->expects($this->once())
            ->method('getVertices')
            ->willReturn([$vertex]);

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

        $face = new Face($imageAnnotatorClient, $this->getFile(), $image);
        $face = $face->detect();

        $this->assertInstanceOf(Generator::class, $face);

        $faces = iterator_to_array($face);

        $this->assertCount(1, $faces);
        $this->assertInstanceOf(FaceData::class, $faces[0]);
        $this->assertEquals('VERY_UNLIKELY', $faces[0]->getAnger());
        $this->assertEquals('VERY_LIKELY', $faces[0]->getJoy());
        $this->assertEquals('POSSIBLE', $faces[0]->getSurprise());
        $this->assertEquals([new VertexData(89.99, 74.22)], $faces[0]->getBounds());
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
            ->willReturnOnConsecutiveCalls(89.99, 50.77, 109.22, 98.39);

        $vertex
            ->expects($this->exactly(4))
            ->method('getY')
            ->willReturnOnConsecutiveCalls(74.22, 68.40, 209.87, 110.28);

        $boundingPoly
            ->expects($this->once())
            ->method('getVertices')
            ->willReturn([$vertex, $vertex, $vertex, $vertex]);

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
            ->with(89.99, 74.22, 109.22, 209.87);

        $face = new Face($imageAnnotatorClient, $this->getFile(), $image);
        $face->drawBoxAroundFaces()->toJpeg('out.jpg');
    }
}
