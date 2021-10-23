<?php

namespace AhmadMayahi\Vision\Tests\Support;

use AhmadMayahi\Vision\Config;
use AhmadMayahi\Vision\Exceptions\ConfigException;
use AhmadMayahi\Vision\Tests\TestCase;

final class ConfigTest extends TestCase
{
    /** @test */
    public function it_should_build_connector_config(): void
    {
        $config = (new Config())
            ->setCredentials($this->googleServiceAccount())
            ->setCredentialsConfig(['useJwtAccessWithScope' => false])
            ->setClientConfig('myconfig')
            ->setApiEndpoint('https://vision.mayahi.net')
            ->setTransport('grpc')
            ->disableRetries();

        $expected = [
            'credentials' => $this->googleServiceAccount(),
            'credentialsConfig' => [
                'useJwtAccessWithScope' => false,
            ],
            'clientConfig' => 'myconfig',
            'apiEndpoint' => 'https://vision.mayahi.net',
            'transport' => 'grpc',
            'disableRetries' => true,
        ];

        $this->assertEquals($expected, $config->getConnectorConfig());
    }

    /** @test */
    public function custom_temp_dir_path()
    {
        $config = (new Config())
            ->setCredentials($this->googleServiceAccount())
            ->setTempDirPath($this->getTempDir());

        $this->assertTrue($config->getTempDirPath() === $this->getTempDir());
    }

    /** @test */
    public function it_should_fail_if_no_credentials_found()
    {
        $this->expectException(ConfigException::class);

        $this->expectExceptionMessage('Could not find the credentials in config!');

        (new Config())->getConnectorConfig();
    }
}
