<?php

namespace AhmadMayahi\GoogleVision\Tests\Detectors;

use AhmadMayahi\GoogleVision\Data\FaceData;
use AhmadMayahi\GoogleVision\Tests\TestCase;
use AhmadMayahi\GoogleVision\Utils\Container;
use AhmadMayahi\GoogleVision\Utils\DrawBoxImage;
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
    public function it_should_get_original_google_vision_response(): void
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
            ->with(file_get_contents($this->getFilePathname()))
            ->willReturn($annotatorImageResponse);

        Container::getInstance()->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $vision = $this
            ->getVision()
            ->faceDetection()
            ->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $vision);
    }

    /** @test */
    public function it_should_get_face_analyzer(): void
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
            ->with(file_get_contents($this->getFilePathname()))
            ->willReturn($annotatorImageResponse);

        $imageAnnotatorClient->expects($this->once())->method('close');

        Container::getInstance()->bind($imageAnnotatorClient, ImageAnnotatorClient::class);

        $stats = $this
            ->getVision($this->getFilePathname())
            ->faceDetection()
            ->detect();

        $this->assertCount(1, $stats);
        $this->assertInstanceOf(FaceData::class, $stats[0]);
        $this->assertEquals('VERY_UNLIKELY', $stats[0]->getAnger());
        $this->assertEquals('VERY_LIKELY', $stats[0]->getJoy());
        $this->assertEquals('POSSIBLE', $stats[0]->getSurprise());
        $this->assertSame([['x' => 100, 'y' => 100]], $stats[0]->getBounds());
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
            ->with(file_get_contents($this->getFilePathname()))
            ->willReturn($annotatorImageResponse);

        $imageAnnotatorClient->expects($this->once())->method('close');

        $outFilename = dirname(__DIR__).DIRECTORY_SEPARATOR . 'files/temp/detect-faces.jpg';

        $drawBoxImage = $this->createMock(DrawBoxImage::class);

        $drawBoxImage
            ->expects($this->exactly(2))
            ->method('drawRectangle')
            ->withConsecutive([330, 24, 546, 276], [77, 23, 273, 250]);

        $drawBoxImage
            ->expects($this->once())
            ->method('saveImage')
            ->with($outFilename);

        Container::getInstance()->bind($imageAnnotatorClient, ImageAnnotatorClient::class);
        Container::getInstance()->bind($drawBoxImage, DrawBoxImage::class);

        $this
            ->getVision()
            ->faceDetection()
            ->drawBoxAroundFaces($outFilename);
    }
}
