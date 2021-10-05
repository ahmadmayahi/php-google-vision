<?php

namespace AhmadMayahi\Vision\Data;

class WebEntityData
{
    public function __construct(private string $entityId, private float $score, private string $description)
    {
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
