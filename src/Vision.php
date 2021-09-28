<?php

namespace AhmadMayahi\Vision;

use AhmadMayahi\Vision\Detectors\Face;
use AhmadMayahi\Vision\Detectors\Image;
use AhmadMayahi\Vision\Detectors\ImageProperties;
use AhmadMayahi\Vision\Detectors\Label;
use AhmadMayahi\Vision\Detectors\Landmark;
use AhmadMayahi\Vision\Detectors\Logo;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer;
use AhmadMayahi\Vision\Detectors\SafeSearch;
use AhmadMayahi\Vision\Utils\File;
use SplFileObject;
use Symfony\Component\Finder\SplFileInfo;

class Vision
{
    /**
     * @var string|resource|SplFileInfo|SplFileObject
     */
    protected $inputFile;

    protected ?string $outputFile = null;

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
    public function inputFile($file): static
    {
        $this->inputFile = $file;

        return $this;
    }

    public function outputFile(string $file)
    {
        $this->outputFile = $file;

        return $this;
    }

    public function faceDetection(): Face
    {
        return new Face($this->getFile(), $this->outputFile);
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

    public function objectLocalizer(): ObjectLocalizer
    {
        return new ObjectLocalizer($this->getFile(), $this->outputFile);
    }

    public function safeSearchDetection(): SafeSearch
    {
        return new SafeSearch($this->getFile());
    }

    private function getFile()
    {
        return new File($this->inputFile, $this->config);
    }
}
