<?php

namespace AhmadMayahi\Vision\Traits;

trait Jsonable
{
    public function asJson(): string
    {
        if ($res = $this->detect()) {
            return json_encode(iterator_to_array($res));
        }

        return json_encode([]);
    }

    abstract public function detect(): mixed;
}
