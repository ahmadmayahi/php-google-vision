<?php

namespace AhmadMayahi\Vision\Contracts;

interface File
{
    public function getLocalPathname();

    /**
     * @return resource|string
     */
    public function toGoogleVisionFile();
}
