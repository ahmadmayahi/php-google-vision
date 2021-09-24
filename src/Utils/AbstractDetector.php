<?php

namespace AhmadMayahi\GoogleVision\Utils;

use AhmadMayahi\GoogleVision\Contracts\File;
use Exception;

abstract class AbstractDetector
{
    /**
     * @throws Exception
     */
    public function __construct(protected File $file)
    {
        $this->registerDependencies();
    }

    abstract protected function registerDependencies(): void;

    abstract public function getOriginalResponse(): mixed;
}
