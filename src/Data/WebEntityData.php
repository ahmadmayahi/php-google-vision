<?php

namespace AhmadMayahi\Vision\Data;

class WebEntityData
{
    public function __construct(public string $entityId, public float $score, public string $description)
    {
    }
}
