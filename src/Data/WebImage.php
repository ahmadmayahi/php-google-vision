<?php

namespace AhmadMayahi\Vision\Data;

class WebImageData
{
    public function __construct(public string $url, public float $score)
    {
    }
}
