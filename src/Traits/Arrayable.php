<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Traits;

trait Arrayable
{
    public function asArray(): array
    {
        $res = $this->detect();

        if (is_iterable($res)) {
            return iterator_to_array($res);
        }

        return [];
    }

    abstract public function detect(): mixed;
}
