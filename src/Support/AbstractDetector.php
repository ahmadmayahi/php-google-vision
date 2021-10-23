<?php

namespace AhmadMayahi\Vision\Support;

use AhmadMayahi\Vision\Contracts\File as FileContract;
use Exception;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

abstract class AbstractDetector
{
    /**
     * @throws Exception
     */
    public function __construct(protected ImageAnnotatorClient $imageAnnotatorClient, protected FileContract $file)
    {
    }

    abstract public function getOriginalResponse(): mixed;

    public function __destruct()
    {
        $this->imageAnnotatorClient->close();
    }
}
