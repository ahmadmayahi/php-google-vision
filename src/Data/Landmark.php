<?php

namespace AhmadMayahi\Vision\Data;

class Landmark
{
    public function __construct(public string $name, public ?array $locations)
    {
    }
}
