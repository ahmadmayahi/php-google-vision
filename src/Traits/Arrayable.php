<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Traits;

trait Arrayable
{
    public function asArray(): array
    {
        if ($detect = $this->detect()) {
            return iterator_to_array($detect);
        }

        return [];
    }

    abstract public function detect(): mixed;
}
