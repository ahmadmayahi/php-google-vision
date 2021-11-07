<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Traits;

trait Jsonable
{
    public function asJson(): string
    {
        $res = $this->detect();

        if (is_iterable($res)) {
            return json_encode(iterator_to_array($res));
        }

        return json_encode([]);
    }

    abstract public function detect(): mixed;
}
