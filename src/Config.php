<?php

namespace AhmadMayahi\Vision;

use AhmadMayahi\Vision\Support\File;
use Exception;
use Google\ApiCore\CredentialsWrapper;
use Google\Auth\FetchAuthTokenInterface;
use SplFileInfo;
use SplFileObject;

class Config
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
        $this->config['file'] = new File($file, $this->getTempDirPath());

        return $this;
    }

    public function getFile(): File
    {
        return $this->config['file'];
    }

    public function setCredentials(string|array|FetchAuthTokenInterface|CredentialsWrapper $val): static
    {
        $this->config['credentials'] = $val;

        return $this;
    }

    public function setCredentialsConfig(array $val): static
    {
        $this->config['credentialsConfig'] = $val;

        return $this;
    }

    public function setRequestTimeout(int $timeout): static
    {
        $this->config['requestTimeout'] = $timeout;

        return $this;
    }

    public function setTempDirPath(string $path): static
    {
        $this->config['tempDirPath'] = $path;

        return $this;
    }

    public function getTempDirPath(): ?string
    {
        return $this->config['tempDirPath'] ?? null;
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
        $config = [
            'credentials' => $this->config['credentials'],
        ];

        if ($endPoint = $this->get('apiEndpoint')) {
            $config['apiEndpoint'] = $endPoint;
        }

        if ($credentialsConfig = $this->get('credentialsConfig')) {
            $config['credentialsConfig'] = $credentialsConfig;
        }

        if ($timeout = $this->get('requestTimeout')) {
            $config['requestTimeout'] = $timeout;
        }

        return $config;
    }

    public function get($key)
    {
        return $this->config[$key] ?? null;
    }
}
