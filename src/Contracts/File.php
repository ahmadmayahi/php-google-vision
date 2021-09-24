<?php

namespace AhmadMayahi\GoogleVision\Contracts;

interface File
{
    public function getLocalPathname();

    /**
     * @return resource|string
     */
    public function toGoogleVisionFile();
}
