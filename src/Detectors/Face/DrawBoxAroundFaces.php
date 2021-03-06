<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors\Face;

use AhmadMayahi\Vision\Contracts\Drawable;
use AhmadMayahi\Vision\Detectors\Face;
use AhmadMayahi\Vision\Enums\Color;
use AhmadMayahi\Vision\Support\Image;

class DrawBoxAroundFaces implements Drawable
{
    private int $borderColor = Color::GREEN;

    public function __construct(private Face $face, private Image $image)
    {
    }

    public function borderColor(int $color): static
    {
        $this->borderColor = $color;

        return $this;
    }

    public function draw(): Image
    {
        /** @var \AhmadMayahi\Vision\Data\Face $face */
        foreach ($this->face->detect() as $face) {
            $vertices = $face->bounds;

            if (0 !== count($vertices)) {
                $x1 = $vertices[0]->x;
                $y1 = $vertices[0]->y;

                $x2 = $vertices[2]->x;
                $y2 = $vertices[2]->y;

                $this->image->drawRectangle($x1, $y1, $x2, $y2, $this->borderColor);
            }
        }

        return $this->image;
    }
}
