<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\FaceData;
use AhmadMayahi\Vision\Detectors\Face;
use AhmadMayahi\Vision\Tests\TestCase;
use AhmadMayahi\Vision\Utils\File;
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

class FaceDetectorTest extends TestCase
{
    /** @test */
    public function it_should_get_face_original_google_vision_response(): void
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

        $face = new Face($imageAnnotatorClient, new File($this->getFilePathname(), $this->getConfig()));
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
        $vertex->expects($this->once())->method('getX')->willReturn(100);
        $vertex->expects($this->once())->method('getY')->willReturn(100);

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
            ->expects($this->any())
            ->method('getFaceAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('faceDetection')
            ->willReturn($annotatorImageResponse);

        $imageAnnotatorClient->expects($this->once())->method('close');

        $face = new Face($imageAnnotatorClient, $this->getFile());
        $face = $face->detect();

        $this->assertInstanceOf(Generator::class, $face);

        $face = iterator_to_array($face);

        $this->assertCount(1, $face);
        $this->assertInstanceOf(FaceData::class, $face[0]);
        $this->assertEquals('VERY_UNLIKELY', $face[0]->getAnger());
        $this->assertEquals('VERY_LIKELY', $face[0]->getJoy());
        $this->assertEquals('POSSIBLE', $face[0]->getSurprise());
        $this->assertSame([['x' => 100, 'y' => 100]], $face[0]->getBounds());
    }

    /** @test */
    public function it_should_draw_box_around_faces()
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $repeatedField = $this->createMock(RepeatedField::class);
        $faceAnnotation1 = $this->createMock(FaceAnnotation::class);
        $faceAnnotation2 = $this->createMock(FaceAnnotation::class);
        $boundingPoly1 = $this->createMock(BoundingPoly::class);
        $boundingPoly2 = $this->createMock(BoundingPoly::class);

        $vertex = $this->createMock(Vertex::class);
        $vertex
            ->method('getX')
            ->willReturnOnConsecutiveCalls(330, 546, 77, 273);
        $vertex
            ->method('getY')
            ->willReturnOnConsecutiveCalls(24, 276, 23, 250);

        $boundingPoly1
            ->expects($this->once())
            ->method('getVertices')
            ->willReturn([
                0 => $vertex,
                2 => $vertex,
            ]);

        $vertex2 = $this->createMock(Vertex::class);
        $vertex2
            ->method('getX')
            ->willReturnOnConsecutiveCalls(330, 546);
        $vertex2
            ->method('getY')
            ->willReturnOnConsecutiveCalls(24, 276);

        $boundingPoly2
            ->expects($this->once())
            ->method('getVertices')
            ->willReturn([
                0 => $vertex,
                2 => $vertex,
            ]);

        $faceAnnotation1
            ->expects($this->once())
            ->method('getBoundingPoly')
            ->willReturn($boundingPoly1);

        $faceAnnotation2
            ->expects($this->once())
            ->method('getBoundingPoly')
            ->willReturn($boundingPoly2);

        $repeatedFieldIter = $this->createMock(RepeatedFieldIter::class);

        $repeatedField
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->mockIterator($repeatedFieldIter, [$faceAnnotation1, $faceAnnotation2]));

        $annotatorImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotatorImageResponse
            ->expects($this->any())
            ->method('getFaceAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('faceDetection')
            ->willReturn($annotatorImageResponse);

        $imageAnnotatorClient->expects($this->once())->method('close');

        $outFilename = dirname(__DIR__).DIRECTORY_SEPARATOR . 'files/temp/detect-faces.jpg';

        $drawBoxImage = $this->createMock(Image::class);

        $drawBoxImage
            ->expects($this->exactly(2))
            ->method('drawRectangle')
            ->withConsecutive([330, 24, 546, 276], [77, 23, 273, 250]);

        $drawBoxImage
            ->expects($this->once())
            ->method('save');

        (new Face($imageAnnotatorClient, $this->getFile(), $drawBoxImage))->drawBoxAroundFaces();
    }
}
