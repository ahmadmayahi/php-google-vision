<?php

namespace AhmadMayahi\Vision;

use AhmadMayahi\Vision\Support\File;
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

    public function setTempDirPath(string $path)
    {
        $this->config['tempDirPath'] = $path;

        return $this;
    }

    public function getTempDirPath()
    {
        return $this->config['tempDirPath'] ?? sys_get_temp_dir();
    }

    /**
     * The address of the API remote host. May optionally include the port, formatted
     * as "<uri>:<port>". Default 'vision.googleapis.com:443'.
     *
     * @param string $endpoint
     * @return $this
     */
    public function setApiEndpoint(string $endpoint): static
    {
        $this->config['apiEndpoint'] = $endpoint;

        return $this;
    }

    public function connectConfig(): array
    {
        return [
            'credentials' => $this->config['credentials'],
        ];
    }
}
