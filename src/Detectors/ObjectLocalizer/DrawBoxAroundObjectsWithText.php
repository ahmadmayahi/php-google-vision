<?php

namespace AhmadMayahi\Vision\Detectors\ObjectLocalizer;

use AhmadMayahi\Vision\Contracts\Drawable;
use AhmadMayahi\Vision\Data\LocalizedObjectData;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Enums\FontEnum;
use AhmadMayahi\Vision\Utils\Image;

class DrawBoxAroundObjectsWithText implements Drawable
{
    private int $boxColor = ColorEnum::GREEN;

    private int $textColor = ColorEnum::RED;

    private int $fontSize = 15;

    private string $font = FontEnum::OPEN_SANS_BOLD;

    public function __construct(private ObjectLocalizer $objectLocalizer, private Image $image)
    {
    }

    public function draw(): Image
    {
        $draw = new DrawBoxAroundObjects($this->objectLocalizer, $this->image);
        $draw->setBoxColor($this->boxColor);

        $draw->setCallback(function (Image $image, LocalizedObjectData $obj) {
            $width = $image->getWidth();
            $height = $image->getHeight();

            $x1 = $obj->getNormalizedVertices()[0]['x'];
            $y2 = $obj->getNormalizedVertices()[2]['y'];

            $image->writeText(
                text: $obj->getName(),
                fontFile: $this->font,
                color: $this->textColor,
                fontSize: $this->fontSize,
                x: ($x1 * $width) + 5,
                y: ($y2 * $height) - 5
            );
        });

        return $draw->draw();
    }

    public function setBoxColor(int $boxColor): static
    {
        $this->boxColor = $boxColor;

        return $this;
    }

    public function setTextColor(int $textColor): static
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function setFontSize(int $fontSize): static
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    public function setFont(string $font): static
    {
        $this->font = $font;

        return $this;
    }
}
