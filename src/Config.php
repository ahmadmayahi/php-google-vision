<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision;

use AhmadMayahi\Vision\Exceptions\ConfigException;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\Transport\TransportInterface;
use Google\Auth\FetchAuthTokenInterface;

class Config
{
    protected array $config = [];

    /**
     * The credentials to be used by the client to authorize API calls. This option
     * accepts either a path to a credentials file, or a decoded credentials file as a PHP array.
     * *Advanced usage*: In addition, this option can also accept a pre-constructed
     * {@see \Google\Auth\FetchAuthTokenInterface} object or
     * {@see \Google\ApiCore\CredentialsWrapper} object. Note that when one of these
     * objects are provided, any settings in $credentialsConfig will be ignored.
     *
     * @return $this
     */
    public function setCredentials(string|array|FetchAuthTokenInterface|CredentialsWrapper $val): static
    {
        $this->config['credentials'] = $val;

        return $this;
    }

    /**
     * Options used to configure credentials, including auth token caching, for the client.
     * For a full list of supporting configuration options:
     * @see \Google\ApiCore\CredentialsWrapper::build()
     *
     * @param array $val
     * @return $this
     */
    public function setCredentialsConfig(array $val): static
    {
        $this->config['credentialsConfig'] = $val;

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
     * @see https://cloud.google.com/vision/docs/reference/rest
     */
    public function setApiEndpoint(string $endpoint): static
    {
        $this->config['apiEndpoint'] = $endpoint;

        return $this;
    }

    /**
     * Determines whether retries defined by the client configuration should be disabled.
     * Default: `false`.
     *
     * @return $this
     */
    public function disableRetries(): static
    {
        return $this->set('disableRetries', true);
    }

    /**
     * Client method configuration, including retry settings. This option can be either
     * a path to a JSON file, or a PHP array containing the decoded JSON data.
     * By default, these settings points to the default client config file, which is
     * provided in the resources' folder.
     *
     * @param string|array $config
     * @return $this
     */
    public function setClientConfig(string|array $config): self
    {
        return $this->set('clientConfig', $config);
    }

    /**
     * The transport used for executing network requests. May be either the string
     * `rest` or `grpc`. Defaults to `grpc` if gRPC support is detected on the system.
     * *Advanced usage*: Additionally, it is possible to pass in an already
     * instantiated {@see \Google\ApiCore\Transport\TransportInterface} object. Note
     * that when this object is provided, any settings in $transportConfig, and any
     * $serviceAddress setting, will be ignored.
     */
    public function setTransport(string|TransportInterface $transport): static
    {
        return $this->set('transport', $transport);
    }

    public function getConnectorConfig(): array
    {
        $config = [
            'credentials' => $this->getOrFail('credentials'),
        ];

        if ($endPoint = $this->get('apiEndpoint')) {
            $config['apiEndpoint'] = $endPoint;
        }

        if ($credentialsConfig = $this->get('credentialsConfig')) {
            $config['credentialsConfig'] = $credentialsConfig;
        }

        if ($transport = $this->get('transport')) {
            $config['transport'] = $transport;
        }

        if ($clientConfig = $this->get('clientConfig')) {
            $config['clientConfig'] = $clientConfig;
        }

        if ($disableRetries = $this->get('disableRetries')) {
            $config['disableRetries'] = $disableRetries;
        }

        return $config;
    }

    public function get($key)
    {
        return $this->config[$key] ?? null;
    }

    protected function set($key, $val)
    {
        $this->config[$key] = $val;

        return $this;
    }

    public function getOrFail($key)
    {
        if (false === array_key_exists($key, $this->config)) {
            throw new ConfigException('Could not find the '.$key.' in config!');
        }

        return $this->config[$key];
    }
}
