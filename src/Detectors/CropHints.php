<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\Detectable;
use AhmadMayahi\Vision\Contracts\File;
use AhmadMayahi\Vision\Data\CropHints as CropHintsData;
use AhmadMayahi\Vision\Data\Vertex as VertexData;
use AhmadMayahi\Vision\Detectors\CropHints\Crop;
use AhmadMayahi\Vision\Detectors\CropHints\DrawBoxAroundHints;
use AhmadMayahi\Vision\Enums\Color;
use AhmadMayahi\Vision\Support\AbstractDetector;
use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Traits\Arrayable;
use AhmadMayahi\Vision\Traits\Jsonable;
use Generator;
use Google\Cloud\Vision\V1\CropHintsAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Vertex;

class CropHints extends AbstractDetector implements Detectable
{
    use Arrayable;
    use Jsonable;

    public function __construct(protected ImageAnnotatorClient $imageAnnotatorClient, protected File $file, protected Image $image)
    {
    }

    public function getOriginalResponse(): ?CropHintsAnnotation
    {
        return $this
            ->imageAnnotatorClient
            ->cropHintsDetection($this->file->toVisionFile())
            ->getCropHintsAnnotation();
    }

    public function detect(): Generator
    {
        /** @var \Google\Cloud\Vision\V1\CropHint $item */
        foreach ($this->getOriginalResponse()->getCropHints() as $item) {
            $bounds = array_map(function (Vertex $vertex) {
                return new VertexData($vertex->getX(), $vertex->getY());
            }, iterator_to_array($item->getBoundingPoly()->getVertices()));

            yield new CropHintsData(
                bounds: $bounds,
                confidence: $item->getConfidence(),
                importanceFraction: $item->getImportanceFraction(),
            );
        }
    }

    public function drawBoxAroundHints(int $borderColor = Color::GREEN): Image
    {
        return (new DrawBoxAroundHints(
            cropHints:  $this,
            image: $this->image,
            borderColor: $borderColor
        ))->draw();
    }

    public function crop(): Image
    {
        return (new Crop($this, $this->image))->crop();
    }
}
