<?php

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
}