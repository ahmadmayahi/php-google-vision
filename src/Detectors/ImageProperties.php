<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\ImagePropertiesData;
use AhmadMayahi\Vision\Traits\HasImageAnnotator;
use AhmadMayahi\Vision\Utils\AbstractDetector;
use Google\Cloud\Vision\V1\ColorInfo;
use Google\Cloud\Vision\V1\ImageProperties as GoogleVisionImageProperties;
use Google\Type\Color;

class ImageProperties extends AbstractDetector
{
    use HasImageAnnotator;

    public function getOriginalResponse(): ?GoogleVisionImageProperties
    {
        $response = $this
            ->getImageAnnotaorClient()
            ->imagePropertiesDetection($this->file->toGoogleVisionFile());

        return $response->getImagePropertiesAnnotation();
    }

    /**
     * @return ImagePropertiesData[]
     */
    public function detect(): array
    {
        $response = $this->getOriginalResponse();

        $results = [];

        /** @var ColorInfo $colorInfo */
        foreach ($response->getDominantColors()->getColors() as $colorInfo) {
            /** @var Color $color */
            $color = $colorInfo->getColor();
            $results[] = new ImagePropertiesData(
                pixelFraction: $colorInfo->getPixelFraction(),
                red: $color->getRed(),
                green: $color->getGreen(),
                blue: $color->getBlue(),
            );
        }

        return $results;
    }
}
