<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Contracts;

interface File
{
    public function getLocalPathname();

    /**
     * @return resource|string
     */
    public function toVisionFile();
}
