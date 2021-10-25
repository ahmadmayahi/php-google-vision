<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Tests\Detectors;

use AhmadMayahi\Vision\Data\LocalizedObject;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer;
use AhmadMayahi\Vision\Enums\Color;
use AhmadMayahi\Vision\Enums\Font;
use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Tests\TestCase;
use Generator;
use Google\Cloud\Vision\V1\AnnotateImageResponse;
use Google\Cloud\Vision\V1\BoundingPoly;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\LocalizedObjectAnnotation;
use Google\Cloud\Vision\V1\NormalizedVertex;
use Google\Protobuf\Internal\RepeatedField;

final class ObjectLocalizerTest extends TestCase
{
    /** @test */
    public function it_should_get_object_localizer_original_response(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);
        $repeatedField = $this->createMock(RepeatedField::class);

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getLocalizedObjectAnnotations')
            ->willReturn($repeatedField);

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('objectLocalization')
            ->willReturn($annotateImageResponse);

        $drawBoxImage = $this->createMock(Image::class);

        $response = (new ObjectLocalizer($imageAnnotatorClient, $this->getFile(), $drawBoxImage))->getOriginalResponse();

        $this->assertInstanceOf(RepeatedField::class, $response);
    }

    /** @test */
    public function it_should_detect_objects(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);

        $annotations = iterator_to_array($this->localizedObjectAnnotationsMocked());

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getLocalizedObjectAnnotations')
            ->willReturn($this->createRepeatedFieldIter($annotations));

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('objectLocalization')
            ->willReturn($annotateImageResponse);

        $image = $this->createMock(Image::class);

        $objectLocalizer = (new ObjectLocalizer($imageAnnotatorClient, $this->getFile(), $image));

        $this->assertEquals($objectLocalizer->asArray(), iterator_to_array($this->localizedObjectAnnotationObjects()));
    }

    /** @test */
    public function it_should_draw_box_around_objects(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);

        $annotations = iterator_to_array($this->localizedObjectAnnotationsMocked());
        $annotationsArr = $this->localizedObjectAnnotations();

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getLocalizedObjectAnnotations')
            ->willReturn($this->createRepeatedFieldIter($annotations));

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('objectLocalization')
            ->willReturn($annotateImageResponse);

        $image = $this->createMock(Image::class);
        $image
            ->method('getWidth')
            ->willReturn(650);
        $image
            ->method('getHeight')
            ->willReturn(340);
        $image
            ->expects($this->exactly(3))
            ->method('drawRectangle')
            ->withConsecutive(
                [
                    intval($annotationsArr[0]['normalizedVertices'][0]['x'] * 650),
                    intval($annotationsArr[0]['normalizedVertices'][0]['y'] * 340),
                    intval($annotationsArr[0]['normalizedVertices'][2]['x'] * 650),
                    intval($annotationsArr[0]['normalizedVertices'][2]['y'] * 340)
                ],
                [
                    intval($annotationsArr[1]['normalizedVertices'][0]['x'] * 650),
                    intval($annotationsArr[1]['normalizedVertices'][0]['y'] * 340),
                    intval($annotationsArr[1]['normalizedVertices'][2]['x'] * 650),
                    intval($annotationsArr[1]['normalizedVertices'][2]['y'] * 340)
                ],
                [
                    intval($annotationsArr[2]['normalizedVertices'][0]['x'] * 650),
                    intval($annotationsArr[2]['normalizedVertices'][0]['y'] * 340),
                    intval($annotationsArr[2]['normalizedVertices'][2]['x'] * 650),
                    intval($annotationsArr[2]['normalizedVertices'][2]['y'] * 340)
                ],
            );

        (new ObjectLocalizer($imageAnnotatorClient, $this->getFile(), $image))
            ->drawBoxAroundObjects()
            ->boxColor(Color::RED)
            ->callback(function (Image $image, LocalizedObject $localizedObject) {
            })
            ->draw();
    }

    /** @test */
    public function it_should_draw_box_around_objects_with_text(): void
    {
        $imageAnnotatorClient = $this->createMock(ImageAnnotatorClient::class);

        $annotations = iterator_to_array($this->localizedObjectAnnotationsMocked());
        $annotationsArr = $this->localizedObjectAnnotations();

        $annotateImageResponse = $this->createMock(AnnotateImageResponse::class);
        $annotateImageResponse
            ->expects($this->once())
            ->method('getLocalizedObjectAnnotations')
            ->willReturn($this->createRepeatedFieldIter($annotations));

        $imageAnnotatorClient
            ->expects($this->once())
            ->method('objectLocalization')
            ->willReturn($annotateImageResponse);

        $image = $this->createMock(Image::class);
        $image
            ->method('getWidth')
            ->willReturn(650);
        $image
            ->method('getHeight')
            ->willReturn(340);
        $image
            ->expects($this->exactly(3))
            ->method('writeText')
            ->withConsecutive(
                [
                    $annotationsArr[0]['name'],
                    Font::OPEN_SANS_MEDIUM,
                    Color::WHITE,
                    12,
                    intval($annotationsArr[0]['normalizedVertices'][0]['x'] * 650) + 5,
                    intval($annotationsArr[0]['normalizedVertices'][2]['y'] * 340) - 5,
                ],
                [
                    $annotationsArr[1]['name'],
                    Font::OPEN_SANS_MEDIUM,
                    Color::WHITE,
                    12,
                    intval($annotationsArr[1]['normalizedVertices'][0]['x'] * 650) + 5,
                    intval($annotationsArr[1]['normalizedVertices'][2]['y'] * 340) - 5,
                ],
                [
                    $annotationsArr[2]['name'],
                    Font::OPEN_SANS_MEDIUM,
                    Color::WHITE,
                    12,
                    intval($annotationsArr[2]['normalizedVertices'][0]['x'] * 650) + 5,
                    intval($annotationsArr[2]['normalizedVertices'][2]['y'] * 340) - 5,
                ],
            );

        (new ObjectLocalizer($imageAnnotatorClient, $this->getFile(), $image))
            ->drawBoxAroundObjectsWithText()
            ->boxColor(Color::RED)
            ->font(Font::OPEN_SANS_MEDIUM)
            ->fontSize(12)
            ->textColor(Color::WHITE)
            ->draw();
    }

    private function localizedObjectAnnotationsMocked(): Generator
    {
        foreach ($this->localizedObjectAnnotations() as $object) {
            $annotation = $this->createMock(LocalizedObjectAnnotation::class);
            $annotation
                ->expects($this->once())
                ->method('getName')
                ->willReturn($object['name']);
            $annotation
                ->expects($this->once())
                ->method('getMid')
                ->willReturn($object['mid']);
            $annotation
                ->expects($this->once())
                ->method('getLanguageCode')
                ->willReturn($object['languageCode']);
            $annotation
                ->expects($this->once())
                ->method('getScore')
                ->willReturn($object['score']);

            $vertices = array_map(function ($item) {
                $normalizedVertex = $this->createMock(NormalizedVertex::class);
                $normalizedVertex
                    ->expects($this->once())
                    ->method('getX')
                    ->willReturn($item['x']);
                $normalizedVertex
                    ->expects($this->once())
                    ->method('getY')
                    ->willReturn($item['y']);

                return $normalizedVertex;
            }, $object['normalizedVertices']);

            $boundingPoly = $this->createMock(BoundingPoly::class);
            $boundingPoly
                ->expects($this->once())
                ->method('getNormalizedVertices')
                ->willReturn($this->createRepeatedFieldIter($vertices));

            $annotation
                ->expects($this->once())
                ->method('getBoundingPoly')
                ->willReturn($boundingPoly);

            yield $annotation;
        }
    }

    private function localizedObjectAnnotations(): array
    {
        return [
            [
                'name' => 'Person',
                'mid' => '/m/01g317',
                'languageCode' => '',
                'score' => 0.8887696862220764,
                'normalizedVertices' =>
                    [
                        [
                            'x' => 0.006587248761206865,
                            'y' => 0.05249844864010811,
                        ],
                        [
                            'x' => 0.47969749569892883,
                            'y' => 0.05249844864010811,
                        ],
                        [
                            'x' => 0.47969749569892883,
                            'y' => 0.9890263676643372,
                        ],
                        [
                            'x' => 0.006587248761206865,
                            'y' => 0.9890263676643372,
                        ],
                    ],
            ],
            [
                'name' => 'Person',
                'mid' => '/m/01g317',
                'languageCode' => '',
                'score' => 0.8861514329910278,
                'normalizedVertices' =>
                    [
                        [
                            'x' => 0.4490935802459717,
                            'y' => 0.028693383559584618,
                        ],
                        [
                            'x' => 0.9935807585716248,
                            'y' => 0.028693383559584618,
                        ],
                        [
                            'x' => 0.9935807585716248,
                            'y' => 0.9960546493530273,
                        ],
                        [
                            'x' => 0.4490935802459717,
                            'y' => 0.9960546493530273,
                        ],
                    ],
            ],
            [
                'name' => 'Glasses',
                'mid' => '/m/0jyfg',
                'languageCode' => '',
                'score' => 0.8430877327919006,
                'normalizedVertices' =>
                    [
                        [
                            'x' => 0.5326752066612244,
                            'y' => 0.35122445225715637,
                        ],
                        [
                            'x' => 0.8008158206939697,
                            'y' => 0.35122445225715637,
                        ],
                        [
                            'x' => 0.8008158206939697,
                            'y' => 0.49574998021125793,
                        ],
                        [
                            'x' => 0.5326752066612244,
                            'y' => 0.49574998021125793,
                        ],
                    ],
            ],
        ];
    }

    private function localizedObjectAnnotationObjects(): Generator
    {
        foreach ($this->localizedObjectAnnotations() as $item) {
            yield new LocalizedObject(
                name: $item['name'],
                mid: $item['mid'],
                languageCode: $item['languageCode'],
                score: $item['score'],
                normalizedVertices: array_map(function ($vertex) {
                    return new \AhmadMayahi\Vision\Data\NormalizedVertex(
                        x: $vertex['x'],
                        y: $vertex['y'],
                    );
                }, $item['normalizedVertices']),
            );
        }
    }
}
