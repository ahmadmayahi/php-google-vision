<?php

namespace AhmadMayahi\Vision\Detectors\ObjectLocalizer;

use AhmadMayahi\Vision\Contracts\Drawable;
use AhmadMayahi\Vision\Data\LocalizedObjectData;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Support\Image;
use Closure;

class DrawBoxAroundObjects implements Drawable
{
    private int $boxColor = ColorEnum::GREEN;

    private ?Closure $callback = null;

    public function __construct(
        private ObjectLocalizer $objectLocalizer,
        private Image $image
    ) {
    }

    public function setBoxColor(int $color): static
    {
        $this->boxColor = $color;

        return $this;
    }

    public function setCallback(?Closure $closure): static
    {
        $this->callback = $closure;

        return $this;
    }

    public function draw(): Image
    {
        $width = $this->image->getWidth();
        $height = $this->image->getHeight();

        /** @var LocalizedObjectData $obj */
        foreach ($this->objectLocalizer->detect() as $obj) {
            $vertices = $obj->normalizedVertices;

            if ($vertices) {
                $x1 = $vertices[0]['x'];
                $y1 = $vertices[0]['y'];

                $x2 = $vertices[2]['x'];
                $y2 = $vertices[2]['y'];

                $this->image->drawRectangle(
                    x1: ($x1 * $width),
                    y1: ($y1 * $height),
                    x2: ($x2 * $width),
                    y2: ($y2 * $height),
                    color: $this->boxColor,
                );

                if ($callback = $this->callback) {
                    $callback($this->image, $obj);
                }
            }
        }

        return $this->image;
    }
}
