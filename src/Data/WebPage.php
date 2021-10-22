<?php

namespace AhmadMayahi\Vision\Data;

class WebPageData
{
    public function __construct(public string $url, public string $title, public float $score)
    {
    }
}
