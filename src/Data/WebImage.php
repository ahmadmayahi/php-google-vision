<?php

namespace AhmadMayahi\Vision\Data;

class WebImage
{
    public function __construct(public string $url, public float $score)
    {
    }
}
