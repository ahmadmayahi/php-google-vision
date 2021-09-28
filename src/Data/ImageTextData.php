<?php

namespace AhmadMayahi\Vision\Data;

class ImageTextData
{
    public function __construct(
        private string $locale,
        private string $text
    ) {
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
