<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

final class WebEntity
{
    public function __construct(public string $entityId, public float $score, public string $description)
    {
    }
}
