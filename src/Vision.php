<?php

namespace AhmadMayahi\GoogleVision;

use AhmadMayahi\GoogleVision\Contracts\File;
use AhmadMayahi\GoogleVision\Detectors\Face;
use AhmadMayahi\GoogleVision\Detectors\Image;
use AhmadMayahi\GoogleVision\Detectors\ImageProperties;
use AhmadMayahi\GoogleVision\Detectors\SafeSearch;

class Vision
{
    public function __construct(private Config $config)
    {
        State::$config = $config;
    }

    public function detectImageText(): Image
    {
        return new Image($this->getFile());
    }

    public function detectFaces(): Face
    {
        return new Face($this->getFile());
    }

    public function detectSafeSearch(): SafeSearch
    {
        return new SafeSearch($this->getFile());
    }

    public function detectImageProperties(): ImageProperties
    {
        return new ImageProperties($this->getFile());
    }

    private function getFile(): File
    {
        return $this->config->getFile();
    }
}
