<?php

namespace AhmadMayahi\Vision\Data;

class VertexData
{
    public function __construct(private float $x, private float $y)
    {

    }

    /**
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }
}
