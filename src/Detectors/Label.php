<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\Detectable;
use AhmadMayahi\Vision\Data\Label as LabelData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use AhmadMayahi\Vision\Traits\Arrayable;
use AhmadMayahi\Vision\Traits\Jsonable;
use Generator;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Protobuf\Internal\RepeatedField;

/**
 * Detect and extract information about entities in an image, across a broad group of categories..\
 *
 * @see https://cloud.google.com/vision/docs/labels
 */
class Label extends AbstractDetector implements Detectable
{
    use Arrayable;
    use Jsonable;

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->imageAnnotatorClient
            ->labelDetection($this->file->toVisionFile());

        return $response->getLabelAnnotations();
    }

    public function detect(): ?Generator
    {
        $response = $this->getOriginalResponse();

        if (0 === $response->count()) {
            return null;
        }

        /** @var EntityAnnotation $item */
        foreach ($response as $item) {
            yield new LabelData(
                description: $item->getDescription(),
            );
        }
    }
}
