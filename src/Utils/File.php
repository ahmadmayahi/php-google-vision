<?php

namespace AhmadMayahi\Vision\Utils;

use AhmadMayahi\Vision\Config;
use AhmadMayahi\Vision\Contracts\File as FileContract;
use AhmadMayahi\Vision\Exceptions\FileNotFoundException;
use Exception;
use SplFileInfo;
use SplFileObject;

class File implements FileContract
{
    /**
     * @param string|resource|SplFileInfo|SplFileObject $file
     *
     */
    public function __construct(private $file, private Config $config)
    {
    }

    /**
     * @return resource|string
     *
     * @throws FileNotFoundException
     */
    public function toGoogleVisionFile()
    {
        if ($this->isGoogleStorage()) {
            /** @var string $file */
            $file = $this->file;

            return $file;
        }

        if (is_string($this->file)) {
            /** @var string $file */
            $file = file_get_contents($this->file);

            return $file;
        }

        if (is_resource($this->file)) {
            return $this->file;
        }

        if ($this->file instanceof SplFileInfo) {
            $fileObj = $this->file->openFile();

            /** @var string $string */
            $file = $fileObj->fread($this->file->getSize());

            return $file;
        }

        throw new FileNotFoundException('File not found!');
    }

    public function getLocalPathname()
    {
        if ($this->isGoogleStorage()) {
            throw new Exception('Google Storage is not supported for this operation!');
        }

        if (is_string($this->file)) {
            return $this->file;
        }

        if (is_resource($this->file)) {
            return $this->resourceTemp();
        }

        if (($this->file instanceof SplFileInfo) || ($this->file instanceof SplFileObject)) {
            return $this->file->getPathname();
        }

        throw new Exception('Cannot get the local file path');
    }

    private function resourceTemp(): string
    {
        $tempFile = tempnam($this->config->getTempDirPath(), sha1(uniqid()));
        file_put_contents($tempFile, stream_get_contents($this->file));

        return $tempFile;
    }

    public function isGoogleStorage(): bool
    {
        return is_string($this->file) && str_starts_with($this->file, 'gs://');
    }
}
