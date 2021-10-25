<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

final class Landmark
{
    /**
     * @param string $name The landmark's name, for example: Taj mahal
     * @param Location[] $locations
     */
    public function __construct(public string $name, public array $locations)
    {
    }
}
