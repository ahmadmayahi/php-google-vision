<?php

namespace AhmadMayahi\GoogleVision\Tests;

use AhmadMayahi\GoogleVision\Config;
use AhmadMayahi\GoogleVision\Utils\Container;
use AhmadMayahi\GoogleVision\Vision;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $tempPath = __DIR__ . DIRECTORY_SEPARATOR.'files/temp' . DIRECTORY_SEPARATOR;

        $files = array_diff(scandir($tempPath), ['.', '..', '.gitignore']);

        foreach ($files as $file) {
            unlink($tempPath . $file);
        }
    }

    protected function getConfig(): Config
    {
        return (new Config())
            ->setFile($this->getFilePathname())
            ->setCredentialsPathname(__DIR__ . DIRECTORY_SEPARATOR . 'files/service-account.json');
    }

    protected function getVision(): Vision
    {
        return new Vision($this->getConfig());
    }

    protected function bind($object, $name): void
    {
        Container::getInstance()->bind($object, $name);
    }

    public function mockIterator(MockObject $iteratorMock, array $items): MockObject
    {
        $iteratorData = new stdClass();
        $iteratorData->array = $items;
        $iteratorData->position = 0;

        $iteratorMock->expects($this->any())
            ->method('rewind')
            ->will(
                $this->returnCallback(
                    function () use ($iteratorData) {
                        $iteratorData->position = 0;
                    }
                )
            );

        $iteratorMock->expects($this->any())
            ->method('current')
            ->will(
                $this->returnCallback(
                    function () use ($iteratorData) {
                        return $iteratorData->array[$iteratorData->position];
                    }
                )
            );

        $iteratorMock->expects($this->any())
            ->method('key')
            ->will(
                $this->returnCallback(
                    function () use ($iteratorData) {
                        return $iteratorData->position;
                    }
                )
            );

        $iteratorMock->expects($this->any())
            ->method('next')
            ->will(
                $this->returnCallback(
                    function () use ($iteratorData) {
                        $iteratorData->position++;
                    }
                )
            );

        $iteratorMock->expects($this->any())
            ->method('valid')
            ->will(
                $this->returnCallback(
                    function () use ($iteratorData) {
                        return isset($iteratorData->array[$iteratorData->position]);
                    }
                )
            );

        return $iteratorMock;
    }

    protected function getFileContents(): bool|string
    {
        return file_get_contents($this->getFilePathname());
    }

    protected function getFilePathname(string $file = null): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'files/input/' . ($file ?? 'google-guys.jpg');
    }
}
