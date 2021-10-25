<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors\CropHints;

use AhmadMayahi\Vision\Detectors\CropHints;
use AhmadMayahi\Vision\Support\Image;

class Crop
{
    public function __construct(private CropHints $cropHints, private Image $image)
    {
    }

    public function crop(): Image
    {
        /** @var \AhmadMayahi\Vision\Data\CropHints $hint */
        foreach ($this->cropHints->detect() as $hint) {
            $bounds = $hint->bounds;

            $this->image->cropImage(
                x: $bounds[0]->x,
                y: $bounds[0]->y,
                width: $bounds[2]->x - 1,
                height: $bounds[2]->y - 1,
            );
        }

        return $this->image;
    }
}
