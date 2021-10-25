<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

final class WebImage
{
    public function __construct(public string $url, public float $score)
    {
    }
}
