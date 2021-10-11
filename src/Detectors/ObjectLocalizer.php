<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\File;
use AhmadMayahi\Vision\Data\LocalizedObjectData;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer\DrawBoxAroundObjects;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer\DrawBoxAroundObjectsWithText;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Enums\FontEnum;
use AhmadMayahi\Vision\Support\AbstractDetector;
use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Traits\Arrayable;
use AhmadMayahi\Vision\Traits\Jsonable;
use Closure;
use Exception;
use Generator;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\LocalizedObjectAnnotation;
use Google\Cloud\Vision\V1\NormalizedVertex;
use Google\Protobuf\Internal\RepeatedField;

class ObjectLocalizer extends AbstractDetector
{
    use Arrayable, Jsonable;

    public function __construct(
        protected ImageAnnotatorClient $imageAnnotatorClient,
        protected File $file,
        protected Image $image
    ) {
    }

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->imageAnnotatorClient
            ->objectLocalization($this->file->toVisionFile());

        return $response->getLocalizedObjectAnnotations();
    }

    public function detect(): ?Generator
    {
        $response = $this->getOriginalResponse();

        if (0 === $response->count()) {
            return null;
        }

        /** @var LocalizedObjectAnnotation $obj */
        foreach ($response as $obj) {
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

    public function drawBoxAroundObjects($color = ColorEnum::GREEN, ?Closure $callback = null): Image
    {
        return (new DrawBoxAroundObjects($this, $this->image))
            ->setBoxColor($color)
            ->setCallback($callback)
            ->draw();
    }

    /**
     * @throws Exception
     */
    public function drawBoxAroundObjectsWithText(int $boxColor = ColorEnum::GREEN, $textColor = ColorEnum::RED, int $fontSize = 15, string $font = FontEnum::OPEN_SANS_BOLD): Image
    {
        return (new DrawBoxAroundObjectsWithText($this, $this->image))
            ->setBoxColor($boxColor)
            ->setTextColor($textColor)
            ->setFontSize($fontSize)
            ->setFont($font)
            ->draw();
    }
}
