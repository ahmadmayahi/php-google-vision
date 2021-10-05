<?php

namespace AhmadMayahi\Vision\Data;

class WebImageData
{
    public function __construct(private string $url, private float $score)
    {
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }
}
