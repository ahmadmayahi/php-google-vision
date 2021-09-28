<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Traits\HasImageAnnotator;
use AhmadMayahi\Vision\Utils\AbstractDetector;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Protobuf\Internal\RepeatedField;

/**
 * @see https://cloud.google.com/vision/docs/labels
 */
class Label extends AbstractDetector
{
    use HasImageAnnotator;

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->getImageAnnotaorClient()
            ->labelDetection($this->file->toGoogleVisionFile());

        return $response->getLabelAnnotations();
    }

    public function detect(): array
    {
        $response = $this->getOriginalResponse();

        $results = [];

        /** @var EntityAnnotation $item */
        foreach ($response as $item) {
            $results[] = $item->getDescription();
        }

        return $results;
    }
}
