<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\ImagePropertiesData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use Generator;
use Google\Cloud\Vision\V1\ColorInfo;
use Google\Cloud\Vision\V1\ImageProperties as GoogleVisionImageProperties;
use Google\Type\Color;

class ImageProperties extends AbstractDetector
{
    public function getOriginalResponse(): ?GoogleVisionImageProperties
    {
        $response = $this
            ->imageAnnotatorClient
            ->imagePropertiesDetection($this->file->toGoogleVisionFile());

        return $response->getImagePropertiesAnnotation();
    }

    public function detect(): Generator
    {
        $response = $this->getOriginalResponse();

        /** @var ColorInfo $colorInfo */
        foreach ($response->getDominantColors()->getColors() as $colorInfo) {
            /** @var Color $color */
            $color = $colorInfo->getColor();
            yield new ImagePropertiesData(
                pixelFraction: $colorInfo->getPixelFraction(),
                red: $color->getRed(),
                green: $color->getGreen(),
                blue: $color->getBlue(),
            );
        }
    }
}
