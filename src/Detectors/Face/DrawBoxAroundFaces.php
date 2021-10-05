<?php

namespace AhmadMayahi\Vision\Detectors\Face;

use AhmadMayahi\Vision\Contracts\Drawable;
use AhmadMayahi\Vision\Data\FaceData;
use AhmadMayahi\Vision\Detectors\Face;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Support\Image;

class DrawBoxAroundFaces implements Drawable
{
    private int $borderColor = ColorEnum::GREEN;

    public function __construct(private Face $face, private Image $image)
    {
    }

    public function setBorderColor(int $color): static
    {
        $this->borderColor = $color;

        return $this;
    }

    public function draw(): Image
    {
        /** @var FaceData $face */
        foreach ($this->face->detect() as $face) {
            $vertices = $face->getBounds();

            if ($vertices) {
                $x1 = $vertices[0]->getX();
                $y1 = $vertices[0]->getY();

                $x2 = $vertices[2]->getX();
                $y2 = $vertices[2]->getY();

                $this->image->drawRectangle($x1, $y1, $x2, $y2, $this->borderColor);
            }
        }

        return $this->image;
    }
}
