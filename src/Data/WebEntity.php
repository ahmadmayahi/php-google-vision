<?php

namespace AhmadMayahi\Vision\Data;

class WebEntity
{
    public function __construct(public string $entityId, public float $score, public string $description)
    {
    }
}
