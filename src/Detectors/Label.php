<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\LabelData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use Generator;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Protobuf\Internal\RepeatedField;

/**
 * @see https://cloud.google.com/vision/docs/labels
 */
class Label extends AbstractDetector
{
    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->imageAnnotatorClient
            ->labelDetection($this->file->toGoogleVisionFile());

        return $response->getLabelAnnotations();
    }

    public function detect(): Generator
    {
        $response = $this->getOriginalResponse();

        /** @var EntityAnnotation $item */
        foreach ($response as $item) {
            yield new LabelData(
                description: $item->getDescription(),
            );
        }
    }
}
