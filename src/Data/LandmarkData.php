<?php

namespace AhmadMayahi\Vision\Data;

class LandmarkData
{
    public function __construct(public string $name, public ?array $locations)
    {
    }
}
