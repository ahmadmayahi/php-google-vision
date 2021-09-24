<?php

namespace AhmadMayahi\GoogleVision\Detectors;

use AhmadMayahi\GoogleVision\Traits\HasImageAnnotator;
use AhmadMayahi\GoogleVision\Utils\AbstractDetector;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Protobuf\Internal\RepeatedField;

/**
 * @see https://cloud.google.com/vision/docs/detecting-logos
 */
class Logo extends AbstractDetector
{
    use HasImageAnnotator;

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->getImageAnnotaorClient()
            ->logoDetection($this->file->toGoogleVisionFile());

        return $response->getLogoAnnotations();
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
