<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors\ObjectLocalizer;

use AhmadMayahi\Vision\Contracts\Drawable;
use AhmadMayahi\Vision\Data\LocalizedObject as LocalizedObjectData;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer;
use AhmadMayahi\Vision\Enums\Color;
use AhmadMayahi\Vision\Support\Image;
use Closure;

class DrawBoxAroundObjects implements Drawable
{
    private int $boxColor = Color::GREEN;

    private ?Closure $closure = null;

    public function __construct(
        private ObjectLocalizer $objectLocalizer,
        private Image $image
    ) {
    }

    public function boxColor(int $color): static
    {
        $this->boxColor = $color;

        return $this;
    }

    public function callback(Closure $closure): static
    {
        $this->closure = $closure;

        return $this;
    }

    public function draw(): Image
    {
        $width = $this->image->getWidth();
        $height = $this->image->getHeight();

        /** @var LocalizedObjectData $obj */
        foreach ($this->objectLocalizer->detect() as $obj) {
            $vertices = $obj->normalizedVertices;

            if (0 !== count($vertices)) {
                $x1 = $vertices[0]->x;
                $y1 = $vertices[0]->y;

                $x2 = $vertices[2]->x;
                $y2 = $vertices[2]->y;

                $this->image->drawRectangle(
                    x1: intval($x1 * $width),
                    y1: intval($y1 * $height),
                    x2: intval($x2 * $width),
                    y2: intval($y2 * $height),
                    color: $this->boxColor,
                );

                if ($callback = $this->closure) {
                    $callback($this->image, $obj);
                }
            }
        }

        return $this->image;
    }
}
