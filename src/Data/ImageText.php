<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Data;

final class ImageText
{
    public function __construct(public string $locale, public string $text)
    {
    }
}
