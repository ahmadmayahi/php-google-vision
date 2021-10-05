<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\LogoData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use Generator;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Protobuf\Internal\RepeatedField;

/**
 * @see https://cloud.google.com/vision/docs/detecting-logos
 */
class Logo extends AbstractDetector
{
    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->imageAnnotatorClient
            ->logoDetection($this->file->toGoogleVisionFile());

        return $response->getLogoAnnotations();
    }

    public function detect(): Generator
    {
        $response = $this->getOriginalResponse();

        /** @var EntityAnnotation $item */
        foreach ($response as $item) {
            yield new LogoData($item->getDescription());
        }
    }
}
