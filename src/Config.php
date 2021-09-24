<?php

namespace AhmadMayahi\GoogleVision;

use AhmadMayahi\GoogleVision\Utils\File;
use Exception;
use SplFileInfo;
use SplFileObject;

final class Config
{
    protected array $config = [];

    /**
     * @param string|resource|SplFileInfo|SplFileObject|File $file
     *
     * @return Config|static
     * @throws Exception
     */
    public function setInputFile($file): static
    {
        $this->config['file'] = new File($file, $this);

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

    public function setTempDirPath(string $path)
    {
        $this->config['tempDirPath'] = $path;

        return $this;
    }

    public function getTempDirPath()
    {
        return $this->config['tempDirPath'] ?? sys_get_temp_dir();
    }

    public function connectConfig(): array
    {
        return [
            'credentials' => $this->config['credentials'],
        ];
    }
}
