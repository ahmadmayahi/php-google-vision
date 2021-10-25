<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Contracts;

use AhmadMayahi\Vision\Support\Image;

interface Drawable
{
    public function draw(): Image;
}
