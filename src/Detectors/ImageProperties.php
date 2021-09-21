<?php

namespace AhmadMayahi\GoogleVision\Detectors;

use AhmadMayahi\GoogleVision\Data\ImagePropertiesData;
use AhmadMayahi\GoogleVision\Traits\HasImageAnnotator;
use AhmadMayahi\GoogleVision\Utils\AbstractExtractor;
use Google\Cloud\Vision\V1\ColorInfo;
use Google\Cloud\Vision\V1\ImageProperties as GoogleVisionImageProperties;
use Google\Type\Color;

class ImageProperties extends AbstractExtractor
{
    use HasImageAnnotator;

    public function getOriginalResponse(): ?GoogleVisionImageProperties
    {
        $response = $this
            ->getImageAnnotaorClient()
            ->imagePropertiesDetection($this->file->getFileContents());

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
