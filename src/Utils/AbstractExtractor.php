<?php

namespace AhmadMayahi\GoogleVision\Utils;

use AhmadMayahi\GoogleVision\Contracts\File;
use Exception;

abstract class AbstractExtractor
{
    /**
     * @throws Exception
     */
    public function __construct(protected File $file)
    {
        if (false === in_array($this->file->getExtension(), static::supportedExtensions())) {
            throw new Exception('File extension not supported!');
        }

        $this->registerDependencies();
    }

    abstract public static function supportedExtensions(): array;

    abstract protected function registerDependencies(): void;

    abstract public function getOriginalResponse();
}
