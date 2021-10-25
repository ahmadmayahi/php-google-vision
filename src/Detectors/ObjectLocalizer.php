<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\Detectable;
use AhmadMayahi\Vision\Contracts\File;
use AhmadMayahi\Vision\Data\LocalizedObject as LocalizedObjectData;
use AhmadMayahi\Vision\Data\NormalizedVertex as NormalizedVertexData;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer\DrawBoxAroundObjects;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer\DrawBoxAroundObjectsWithText;
use AhmadMayahi\Vision\Support\AbstractDetector;
use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Traits\Arrayable;
use AhmadMayahi\Vision\Traits\Jsonable;
use Generator;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\LocalizedObjectAnnotation;
use Google\Cloud\Vision\V1\NormalizedVertex;
use Google\Protobuf\Internal\RepeatedField;

class ObjectLocalizer extends AbstractDetector implements Detectable
{
    use Arrayable;
    use Jsonable;

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
                normalizedVertices: array_map(function (NormalizedVertex $vertex) {
                    return new NormalizedVertexData(
                        x: $vertex->getX(),
                        y: $vertex->getY()
                    );
                }, iterator_to_array($vertices)),
            );
        }
    }

    public function drawBoxAroundObjects(): DrawBoxAroundObjects
    {
        return new DrawBoxAroundObjects($this, $this->image);
    }

    public function drawBoxAroundObjectsWithText(): DrawBoxAroundObjectsWithText
    {
        return (new DrawBoxAroundObjectsWithText($this, $this->image));
    }
}
