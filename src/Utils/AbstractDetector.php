<?php

namespace AhmadMayahi\Vision\Utils;

use AhmadMayahi\Vision\Contracts\File;
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
