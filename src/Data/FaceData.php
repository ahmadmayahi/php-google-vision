<?php

namespace AhmadMayahi\Vision\Data;

class FaceData
{
    public function __construct(
        private string $anger,
        private string $joy,
        private string $surprise,
        private array $bounds,
    ) {
    }

    /**
     * @return string
     */
    public function getAnger(): string
    {
        return $this->anger;
    }

    /**
     * @return string
     */
    public function getJoy(): string
    {
        return $this->joy;
    }

    /**
     * @return string
     */
    public function getSurprise(): string
    {
        return $this->surprise;
    }

    /**
     * @return array
     */
    public function getBounds(): array
    {
        return $this->bounds;
    }
}
