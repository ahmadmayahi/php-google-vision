<?php

namespace AhmadMayahi\GoogleVision;

use AhmadMayahi\GoogleVision\Detectors\Face;
use AhmadMayahi\GoogleVision\Detectors\Image;
use AhmadMayahi\GoogleVision\Detectors\ImageProperties;
use AhmadMayahi\GoogleVision\Detectors\Label;
use AhmadMayahi\GoogleVision\Detectors\Landmark;
use AhmadMayahi\GoogleVision\Detectors\Logo;
use AhmadMayahi\GoogleVision\Detectors\SafeSearch;
use AhmadMayahi\GoogleVision\Utils\File;
use SplFileObject;
use Symfony\Component\Finder\SplFileInfo;

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

    /**
     * @param string|resource|SplFileInfo|SplFileObject $file
     *
     * @return Vision|static
     * @throws \Exception
     */
    public function file($file): static
    {
        $this->config->setInputFile($file);

        return $this;
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

    public function labelDetection(): Label
    {
        return new Label($this->getFile());
    }

    public function logoDetection(): Logo
    {
        return new Logo($this->getFile());
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
