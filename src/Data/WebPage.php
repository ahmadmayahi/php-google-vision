<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

final class WebPage
{
    public function __construct(public string $url, public string $title, public float $score)
    {
    }
}
