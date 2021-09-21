<?php

namespace AhmadMayahi\GoogleVision;

use AhmadMayahi\GoogleVision\Contracts\File;
use AhmadMayahi\GoogleVision\Detectors\Face;
use AhmadMayahi\GoogleVision\Detectors\Image;
use AhmadMayahi\GoogleVision\Detectors\ImageProperties;
use AhmadMayahi\GoogleVision\Detectors\Landmark;
use AhmadMayahi\GoogleVision\Detectors\SafeSearch;

class Vision
{
    public function __construct(private Config $config)
    {
        State::$config = $config;
    }

    public static function new(Config $config): static
    {
        return new self($config);
    }

    public function faceDetection(): Face
    {
        return new Face($this->getFile());
    }

    public function imageTextDetection(): Image
    {
        return new Image($this->getFile());
    }

    public function imagePropertiesDetection(): ImageProperties
    {
        return new ImageProperties($this->getFile());
    }

    public function landmarkDetection(): Landmark
    {
        return new Landmark($this->getFile());
    }

    public function safeSearchDetection(): SafeSearch
    {
        return new SafeSearch($this->getFile());
    }

    protected function getFile(): File
    {
        return $this->config->getFile();
    }
}
