<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\LocalizedObjectData;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Enums\FontEnum;
use AhmadMayahi\Vision\Traits\HasImageAnnotator;
use AhmadMayahi\Vision\Utils\AbstractDetector;
use AhmadMayahi\Vision\Utils\Container;
use AhmadMayahi\Vision\Utils\File;
use AhmadMayahi\Vision\Utils\Image;
use Exception;
use Generator;
use Google\Cloud\Vision\V1\LocalizedObjectAnnotation;
use Google\Cloud\Vision\V1\NormalizedVertex;
use Google\Protobuf\Internal\RepeatedField;

class ObjectLocalizer extends AbstractDetector
{
    use HasImageAnnotator;

    public function __construct(File $file, private ?string $outputFile = null)
    {
        parent::__construct($file);

        Container::getInstance()->bindOnce(Image::class);
    }

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->getImageAnnotaorClient()
            ->objectLocalization($this->file->toGoogleVisionFile());

        return $response->getLocalizedObjectAnnotations();
    }

    public function detect(): Generator
    {
        /** @var LocalizedObjectAnnotation $obj */
        foreach ($this->getOriginalResponse() as $obj) {
            $vertices = $obj->getBoundingPoly()->getNormalizedVertices();

            yield new LocalizedObjectData(
                name: $obj->getName(),
                mid: $obj->getMid(),
                languageCode: $obj->getLanguageCode(),
                score: $obj->getScore(),
                normalizedVertices: array_map(function (NormalizedVertex $item) {
                    return [
                        'x' => $item->getX(),
                        'y' => $item->getY(),
                    ];
                }, iterator_to_array($vertices)),
            );
        }
    }

    public function drawBoxAroundObjects($color = ColorEnum::GREEN, ?callable $callback = null)
    {
        $path = $this->file->getLocalPathname();

        if (false === copy($path, $this->outputFile)) {
            throw new Exception('Could not copy the file');
        }

        /** @var Image $outputImage */
        $outputImage = Container::getInstance()->get(Image::class, $this->outputFile);

        $width = $outputImage->getWidth();
        $height = $outputImage->getHeight();

        /** @var LocalizedObjectData $obj */
        foreach ($this->detect() as $obj) {
            $vertices = $obj->getNormalizedVertices();

            if ($vertices) {
                $x1 = $vertices[0]['x'];
                $y1 = $vertices[0]['y'];

                $x2 = $vertices[2]['x'];
                $y2 = $vertices[2]['y'];

                $outputImage->drawRectangle(
                    x1: ($x1 * $width),
                    y1: ($y1 * $height),
                    x2: ($x2 * $width),
                    y2: ($y2 * $height),
                    color: $color,
                );

                if ($callback) {
                    $callback($outputImage, $obj);
                }
            }
        }

        $outputImage->saveImage($this->outputFile);
    }

    /**
     * @throws Exception
     */
    public function drawBoxAroundObjectsWithText(int $boxColor = ColorEnum::GREEN, $textColor = ColorEnum::RED, int $fontSize = 15, string $font = FontEnum::OPEN_SANS_BOLD)
    {
        $this->drawBoxAroundObjects($boxColor, function (Image $image, LocalizedObjectData $obj) use ($textColor, $fontSize, $font) {
            $width = $image->getWidth();
            $height = $image->getHeight();

            $x1 = $obj->getNormalizedVertices()[0]['x'];
            $y2 = $obj->getNormalizedVertices()[2]['y'];

            $image->writeText(
                text: $obj->getName(),
                fontFile: $font,
                color: $textColor,
                fontSize: $fontSize,
                x: ($x1 * $width) + 5,
                y: ($y2 * $height) - 5
            );
        });
    }
}