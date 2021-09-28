<?php

namespace AhmadMayahi\Vision\Data;

class LandmarkData
{
    public function __construct(
        private string $name,
        private array $locations
    ) {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getLocations(): array
    {
        return $this->locations;
    }
}
