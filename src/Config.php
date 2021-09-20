<?php

namespace AhmadMayahi\GoogleVision;

use AhmadMayahi\GoogleVision\Contracts\File;
use AhmadMayahi\GoogleVision\Utils\LocalFile;
use Exception;

final class Config
{
    protected array $config = [];

    /**
     * @throws Exception
     */
    public function setFile(string $pathname): static
    {
        if (false === file_exists($pathname)) {
            throw new Exception('File '.$pathname.' does not exist!');
        }

        $this->config['file'] = new LocalFile($pathname);

        return $this;
    }

    public function getFile(): File
    {
        return $this->config['file'];
    }

    public function setCredentialsPathname(string $path): static
    {
        $this->config['credentials'] = $path;

        return $this;
    }

    public function setRequestTimeout(int $timeout): static
    {
        $this->config['requestTimeout'] = $timeout;

        return $this;
    }

    public function setBucket(string $bucket): static
    {
        $this->config['bucket'] = $bucket;

        return $this;
    }

    public function getBucket(): ?string
    {
        return $this->config['bucket'] ?? null;
    }

    public function connectConfig(): array
    {
        return [
            'credentials' => $this->config['credentials'],
        ];
    }
}
