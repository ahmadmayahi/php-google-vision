<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\Detectable;
use AhmadMayahi\Vision\Data\ImageProperties as ImagePropertiesData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use AhmadMayahi\Vision\Traits\Arrayable;
use AhmadMayahi\Vision\Traits\Jsonable;
use Generator;
use Google\Cloud\Vision\V1\ColorInfo;
use Google\Cloud\Vision\V1\ImageProperties as GoogleVisionImageProperties;
use Google\Type\Color;

class ImageProperties extends AbstractDetector implements Detectable
{
    use Arrayable;
    use Jsonable;

    public function getOriginalResponse(): ?GoogleVisionImageProperties
    {
        $response = $this
            ->imageAnnotatorClient
            ->imagePropertiesDetection($this->file->toVisionFile());

        return $response->getImagePropertiesAnnotation();
    }

    public function detect(): ?Generator
    {
        $response = $this->getOriginalResponse();

        if (! $response) {
            return null;
        }

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
