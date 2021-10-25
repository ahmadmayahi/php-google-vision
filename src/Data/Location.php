<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

final class Location
{
    public function __construct(public float $latitude, public float $longitude)
    {
    }
}
