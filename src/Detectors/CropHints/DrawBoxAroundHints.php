<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors\CropHints;

use AhmadMayahi\Vision\Detectors\CropHints;
use AhmadMayahi\Vision\Enums\Color;
use AhmadMayahi\Vision\Support\Image;

class DrawBoxAroundHints
{
    public function __construct(private CropHints $cropHints, private Image $image, private int $borderColor = Color::GREEN)
    {
    }

    public function draw(): Image
    {
        /** @var \AhmadMayahi\Vision\Data\CropHints $hint */
        foreach ($this->cropHints->detect() as $hint) {
            $bounds = $hint->bounds;

            $this->image->drawPolygon([
                $bounds[0]->x,
                $bounds[0]->y,
                $bounds[1]->x,
                $bounds[1]->y,
                $bounds[2]->x,
                $bounds[2]->y,
                $bounds[3]->x,
                $bounds[3]->y,
            ], 4, $this->borderColor);
        }

        return $this->image;
    }
}
