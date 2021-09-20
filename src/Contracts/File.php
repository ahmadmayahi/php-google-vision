<?php

namespace AhmadMayahi\GoogleVision\Contracts;

interface File
{
    public function getExtension();

    public function getPathname();

    public function getFileContents();
}
