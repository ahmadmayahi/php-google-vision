<?php

namespace AhmadMayahi\Vision;

use AhmadMayahi\Vision\Detectors\CropHints as CropHintsDetector;
use AhmadMayahi\Vision\Detectors\Face as FaceDetector;
use AhmadMayahi\Vision\Detectors\ImageProperties as ImagePropertiesDetector;
use AhmadMayahi\Vision\Detectors\ImageText as ImageTextDetector;
use AhmadMayahi\Vision\Detectors\Label as LabelDetector;
use AhmadMayahi\Vision\Detectors\Landmark as LandmarkDetector;
use AhmadMayahi\Vision\Detectors\Logo as LogoDetector;
use AhmadMayahi\Vision\Detectors\ObjectLocalizer as ObjectLocalizerDetector;
use AhmadMayahi\Vision\Detectors\SafeSearch as SafeSearchDetector;
use AhmadMayahi\Vision\Detectors\Web as WebDetector;
use AhmadMayahi\Vision\Exceptions\FileException;
use AhmadMayahi\Vision\Support\File;
use AhmadMayahi\Vision\Support\Image;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class Vision
{
    /**
     * @var string|resource|\SplFileInfo|\SplFileObject
     */
    protected $inputFile = null;

    protected static ?self $instance = null;

    protected function __construct(protected Config $config, protected ImageAnnotatorClient $annotatorClient)
    {
    }

    public static function init(Config $config, ImageAnnotatorClient $client = null): static
    {
        $client = $client ?? new ImageAnnotatorClient($config->getConnectorConfig());

        if (is_null(self::$instance)) {
            self::$instance = new self($config, $client);
        }

        return self::$instance;
    }

    /**
     * @param string|resource|\SplFileInfo|\SplFileObject $file
     *
     * @return Vision|static
     * @throws \Exception
     */
    public function file($file): static
    {
        $this->inputFile = $file;

        return $this;
    }

    public function cropHintsDetection(): CropHintsDetector
    {
        return new CropHintsDetector(
            $this->annotatorClient,
            $this->getFile(),
            new Image($this->getFile()),
        );
    }

    public function faceDetection(): FaceDetector
    {
        return new FaceDetector(
            imageAnnotatorClient: $this->annotatorClient,
            file: $this->getFile(),
            image: new Image($this->getFile()),
        );
    }

    public function imageTextDetection(): ImageTextDetector
    {
        return new ImageTextDetector($this->annotatorClient, $this->getFile());
    }

    public function imagePropertiesDetection(): ImagePropertiesDetector
    {
        return new ImagePropertiesDetector($this->annotatorClient, $this->getFile());
    }

    public function labelDetection(): LabelDetector
    {
        return new LabelDetector($this->annotatorClient, $this->getFile());
    }

    public function landmarkDetection(): LandmarkDetector
    {
        return new LandmarkDetector($this->annotatorClient, $this->getFile());
    }

    public function logoDetection(): LogoDetector
    {
        return new LogoDetector($this->annotatorClient, $this->getFile());
    }

    public function objectLocalizer(): ObjectLocalizerDetector
    {
        return new ObjectLocalizerDetector(
            imageAnnotatorClient:  $this->annotatorClient,
            file: $this->getFile(),
            image: new Support\Image($this->getFile()),
        );
    }

    public function safeSearchDetection(): SafeSearchDetector
    {
        return new SafeSearchDetector($this->annotatorClient, $this->getFile());
    }

    public function webDetection(): WebDetector
    {
        return new WebDetector($this->annotatorClient, $this->getFile());
    }

    protected function getFile(): File
    {
        if (! $this->inputFile) {
            throw new FileException('Please specify the file!');
        }

        return new File($this->inputFile, $this->config->getTempDirPath());
    }

    public function __clone()
    {
    }

    public function __wakeup()
    {
    }

    public function __sleep(): array
    {
        return [];
    }
}
