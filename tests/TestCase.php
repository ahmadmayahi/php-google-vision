<?php

namespace AhmadMayahi\Vision\Tests;

use AhmadMayahi\Vision\Config;
use AhmadMayahi\Vision\Support\File;
use Google\Protobuf\Internal\RepeatedField as RepeatedFieldVision;
use Google\Protobuf\Internal\RepeatedFieldIter;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @after */
    public function tempCleanUp(): void
    {
        $tempPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'files/temp' . DIRECTORY_SEPARATOR;

        $files = array_diff(scandir($tempPath), ['.', '..', '.gitignore']);

        foreach ($files as $file) {
            @unlink($tempPath . $file);
        }
    }

    protected function googleServiceAccount(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'files/service-account.json';
    }

    protected function getConfig(): Config
    {
        return (new Config())
            ->setCredentials($this->googleServiceAccount());
    }

    private function mockIterator(MockObject $iteratorMock, array $items): MockObject
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

    protected function getFilePathname(string $file = null): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'files/input/' . ($file ?? 'larry-and-sergey.jpg');
    }

    protected function getTempDir(string $file = null): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'files/temp/' . $file;
    }

    protected function getFile(string $filename = null): File
    {
        return new File($this->getFilePathname($filename), $this->getTempDir());
    }

    protected function createRepeatedField(array $array): RepeatedFieldVision
    {
        return new RepeatedField($array);
    }

    protected function createRepeatedFieldIter($container)
    {
        $repeatedFieldIter = $this->createMock(RepeatedFieldIter::class);
        $repeatedField = $this->createMock(RepeatedFieldVision::class);
        $repeatedField
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($this->mockIterator($repeatedFieldIter, $container));

        return $repeatedField;
    }
}
