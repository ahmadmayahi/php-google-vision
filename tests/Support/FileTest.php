<?php

namespace AhmadMayahi\Vision\Tests\Utils;

use AhmadMayahi\Vision\Support\File;
use AhmadMayahi\Vision\Tests\TestCase;
use SplFileInfo;
use SplFileObject;

final class FileTest extends TestCase
{
    /** @test */
    public function it_should_accept_file_path(): void
    {
        $filePath = $this->getFilePathname();

        $file = new File($filePath, $this->getTempDir());

        $this->assertEquals($filePath, $file->getLocalPathname());

        $this->assertEquals(file_get_contents($filePath), $file->toVisionFile());
    }

    /** @test */
    public function it_should_accept_splFileInfo(): void
    {
        $filePath = $this->getFilePathname();

        $splFileInfo = new SplFileInfo($filePath);

        $file = new File($splFileInfo, $this->getTempDir());

        $this->assertEquals($this->getFilePathname(), $file->getLocalPathname());

        $this->assertEquals(file_get_contents($filePath), $file->toVisionFile());
    }

    /** @test */
    public function it_should_read_accept_splFileObject(): void
    {
        $filePath = $this->getFilePathname();

        $splFileObject = new SplFileObject($filePath);

        $file = new File($splFileObject, $this->getTempDir());

        $this->assertEquals($this->getFilePathname(), $file->getLocalPathname());

        $this->assertEquals(file_get_contents($filePath), $file->toVisionFile());
    }

    /** @test */
    public function it_should_accept_resource(): void
    {
        $resource = fopen($this->getFilePathname(), 'r');

        $config = $this->getConfig()->setTempDirPath($this->getTempDir());

        $file = new File($resource, $this->getTempDir());

        $this->assertFileExists($file->getLocalPathname());

        $this->assertIsResource($file->toVisionFile());

        $this->assertSame($resource, $file->toVisionFile());
    }

    /** @test */
    public function it_should_accept_google_storage_file(): void
    {
        $storage = 'gs://path/to/my/file.jpg';

        $file = new File($storage, $this->getTempDir());

        $this->assertTrue($file->isGoogleStoragePath());

        $this->assertEquals($storage, $file->toVisionFile());
    }
}
