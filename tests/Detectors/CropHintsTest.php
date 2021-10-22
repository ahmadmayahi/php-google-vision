<?php

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\Vertex as VertexData;
use AhmadMayahi\Vision\Detectors\CropHints;
use AhmadMayahi\Vision\Data\CropHints as CropHintsData;
use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Tests\TestCase;
use Generator;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\BoundingPoly;
use Google\Cloud\Vision\V1\CropHint;
use Google\Cloud\Vision\V1\CropHintsAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Vertex;

final class CropHintsTest extends TestCase
{
    /** @test */
    public function it_should_crop_hints_get_original_response(): void
    {
        $cropHintsAnnotation = $this->createMock(CropHintsAnnotation::class);

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getCropHintsAnnotation')
            ->willReturn($cropHintsAnnotation);

        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $imageAnnotatorClient
            ->expects($this->once())
            ->method('cropHintsDetection')
            ->willReturn($annotateImageResponse);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('close');

        $image = $this->createMock(Image::class);

        (new CropHints($imageAnnotatorClient, $this->getFile(), $image))
            ->getOriginalResponse();
    }

    /** @test */
    public function it_should_detect_crop_hints(): void
    {
        $imageAnnotatorClient = $this->getImageAnnotate();

        $image = $this->createMock(Image::class);

        $result = (new CropHints($imageAnnotatorClient, $this->getFile(), $image))
            ->asArray();

        $this->assertEquals(1, count($result));

        /** @var CropHintsData $cropHintData */
        $cropHintData = $result[0];

        $bounds = iterator_to_array($this->getVertices());

        $this->assertEquals($bounds, $cropHintData->bounds);
        $this->assertEquals(0.5, $cropHintData->confidence);
        $this->assertEquals(0.79706966876984, $cropHintData->importanceFraction);
    }

    /** @test */
    public function it_should_draw_box_around_hints(): void
    {
        $imageAnnotatorClient = $this->getImageAnnotate();

        $bounds = $this->getBoundValues();

        $image = $this->createMock(Image::class);
        $image
            ->expects($this->once())
            ->method('drawPolygon')
            ->with([
                $bounds[0]['x'],
                $bounds[0]['y'],
                $bounds[1]['x'],
                $bounds[1]['y'],
                $bounds[2]['x'],
                $bounds[2]['y'],
                $bounds[3]['x'],
                $bounds[3]['y'],
            ]);
        $image
            ->expects($this->once())
            ->method('toJpeg')
            ->willReturn(true);

        (new CropHints($imageAnnotatorClient, $this->getFile(), $image))
            ->drawBoxAroundHints()
            ->toJpeg('out.png');
    }

    /** @test */
    public function it_should_crop_hints()
    {
        $imageAnnotatorClient = $this->getImageAnnotate();

        $bounds = $this->getBoundValues();

        $image = $this->createMock(Image::class);
        $image
            ->expects($this->once())
            ->method('cropImage')
            ->with(
                $bounds[0]['x'],
                $bounds[0]['y'],
                $bounds[2]['x'] - 1,
                $bounds[2]['y'] - 1,
            );
        $image
            ->expects($this->once())
            ->method('toJpeg')
            ->willReturn(true);

        (new CropHints($imageAnnotatorClient, $this->getFile(), $image))
            ->crop()
            ->toJpeg('out.png');
    }

    private function getImageAnnotate()
    {
        $cropHintsAnnotation = $this->createMock(CropHintsAnnotation::class);
        $cropHintsAnnotation
            ->expects($this->once())
            ->method('getCropHints')
            ->willReturn($this->createRepeatedFieldIter([$this->cropHint()]));

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getCropHintsAnnotation')
            ->willReturn($cropHintsAnnotation);

        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $imageAnnotatorClient
            ->expects($this->once())
            ->method('cropHintsDetection')
            ->willReturn($annotateImageResponse);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('close');

        return $imageAnnotatorClient;
    }

    private function getBoundValues(): array
    {
        return [
            ['x' => 130, 'y' => 0],
            ['x' => 470, 'y' => 0],
            ['x' => 470, 'y' => 339],
            ['x' => 130, 'y' => 339],
        ];
    }

    private function getVertices(): Generator
    {
        foreach ($this->getBoundValues() as ['x' => $x, 'y' => $y]) {
            yield new VertexData(
                x: $x,
                y: $y
            );
        }
    }

    private function cropHint(): CropHint
    {
        $vertices = [];
        foreach ($this->getBoundValues() as $bound) {
            $vertex = $this->createMock(Vertex::class);
            $vertex
                ->method('getX')
                ->willReturn($bound['x']);
            $vertex
                ->method('getY')
                ->willReturn($bound['y']);
            $vertices[] = $vertex;
        }

        $boundingPoly = $this->createMock(BoundingPoly::class);
        $boundingPoly
            ->expects($this->once())
            ->method('getVertices')
            ->willReturn($this->createRepeatedFieldIter($vertices));

        $cropHint = $this->createMock(CropHint::class);
        $cropHint
            ->expects($this->once())
            ->method('getBoundingPoly')
            ->willReturn($boundingPoly);
        $cropHint
            ->expects($this->once())
            ->method('getConfidence')
            ->willReturn(0.5);
        $cropHint
            ->expects($this->once())
            ->method('getImportanceFraction')
            ->willReturn(0.79706966876984);

        return $cropHint;
    }
}
