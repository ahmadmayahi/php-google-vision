<?php

namespace AhmadMayahi\Vision\Contracts;

use AhmadMayahi\Vision\Utils\Image;

interface Drawable
{
    public function draw(): Image;
}
