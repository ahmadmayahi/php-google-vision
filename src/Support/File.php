<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Support;

use AhmadMayahi\Vision\Contracts\File as FileContract;
use AhmadMayahi\Vision\Exceptions\FileException;
use AhmadMayahi\Vision\Exceptions\UnsupportedFileTypeException;
use Exception;
use SplFileInfo;
use SplFileObject;

class File implements FileContract
{
    /**
     * @param string|resource|SplFileInfo|SplFileObject $file Input file
     * @param null|string $tempDirPath Full path to temporary directory
     */
    public function __construct(private $file, private ?string $tempDirPath = null)
    {
    }

    /**
     * Generates vision compatible to be used as a file input in Google\Cloud\Vision\V1\ImageAnnotatorClient
     *
     * @return resource|string
     * @throws FileException
     */
    public function toVisionFile()
    {
        if (is_resource($this->file)) {
            return $this->file;
        }

        if ($this->file instanceof SplFileInfo) {
            $fileObj = $this->file->openFile();

            return (string) $fileObj->fread($this->file->getSize());
        }

        if ($this->isGoogleStoragePath()) {
            return (string) $this->file;
        }

        if ($this->isLocalFile()) {
            return (string) file_get_contents($this->file);
        }

        throw new FileException('File not found or not compatible!');
    }

    public function getLocalPathname(): string
    {
        if ($this->isGoogleStoragePath()) {
            throw new Exception('Google Storage is not supported for this operation!');
        }

        if (is_resource($this->file)) {
            return $this->getStreamFilePath();
        }

        if ($this->file instanceof SplFileInfo) {
            return $this->file->getPathname();
        }

        if ($this->isLocalFile()) {
            return (string) $this->file;
        }

        throw new FileException('Cannot get the local file path');
    }

    public function getStreamFilePath(): string
    {
        if (false === $this->isResource()) {
            throw new FileException('File is not resource!');
        }

        $tempFile = $this->createTempFile();
        $this->saveStream($tempFile);

        return $tempFile;
    }

    private function createTempFile(): string
    {
        return tempnam($this->getTempDir(), sha1(uniqid()));
    }

    private function saveStream(string $tempFile): void
    {
        file_put_contents($tempFile, stream_get_contents($this->file));
    }

    public function getContents()
    {
        if ($this->isGoogleStoragePath()) {
            throw new UnsupportedFileTypeException('Google Storage is not supported!');
        }

        if ($this->isLocalFile()) {
            return file_get_contents($this->file);
        }

        if (is_resource($this->file)) {
            return stream_get_contents($this->file);
        }

        if ($this->file instanceof SplFileInfo) {
            return file_get_contents($this->file->getPathname());
        }
    }

    public function isGoogleStoragePath(): bool
    {
        return is_string($this->file) && str_starts_with($this->file, 'gs://');
    }

    public function isResource(): bool
    {
        return is_resource($this->file);
    }

    public function isLocalFile(): bool
    {
        return is_string($this->file) && file_exists($this->file);
    }

    private function getTempDir(): string
    {
        return $this->tempDirPath ?? sys_get_temp_dir();
    }
}
