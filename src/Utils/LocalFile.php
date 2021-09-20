<?php

namespace AhmadMayahi\GoogleVision\Utils;

use AhmadMayahi\GoogleVision\Contracts\File;
use Exception;
use Generator;
use SplFileInfo;

class LocalFile extends SplFileInfo implements File
{
    /**
     * @throws Exception
     */
    public function getFileContents(): string
    {
        if (false === $this->isReadable()) {
            throw new Exception('File is not readable!');
        }

        $fileObj = $this->openFile();

        return $fileObj->fread($this->getSize());
    }

    public function readFileUsingGenerator(): Generator
    {
        $handle = $this->openFile();

        while (false === $handle->eof()) {
            yield $handle->fgets();
        }
    }
}
