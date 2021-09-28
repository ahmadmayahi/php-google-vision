<?php

namespace AhmadMayahi\Vision\Data;

class LogoData
{
    public function __construct(
        private string $description
    ) {
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
