<?php

namespace AhmadMayahi\Vision\Support;

use AhmadMayahi\Vision\Contracts\File;
use Exception;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

abstract class AbstractDetector
{
    /**
     * @throws Exception
     */
    public function __construct(protected ImageAnnotatorClient $imageAnnotatorClient, protected File $file)
    {
    }

    abstract public function getOriginalResponse(): mixed;

    public function __destruct()
    {
        $this->imageAnnotatorClient->close();
    }
}
