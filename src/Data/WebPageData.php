<?php

namespace AhmadMayahi\Vision\Data;

class WebPageData
{
    public function __construct(private string $url, private string $title, private float $score)
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }
}
