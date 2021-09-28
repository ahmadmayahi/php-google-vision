<?php

namespace AhmadMayahi\Vision\Tests\Utils;

use AhmadMayahi\Vision\Tests\TestCase;
use AhmadMayahi\Vision\Utils\File;
use SplFileInfo;
use SplFileObject;

final class FileTest extends TestCase
{
    /** @test */
    public function it_should_accept_file_path(): void
    {
        $filePath = $this->getFilePathname();

        $file = new File($filePath, $this->getConfig());

        $this->assertEquals($filePath, $file->getLocalPathname());

        $this->assertEquals(file_get_contents($filePath), $file->toGoogleVisionFile());
    }

    /** @test */
    public function it_should_accept_splFileInfo(): void
    {
        $filePath = $this->getFilePathname();

        $splFileInfo = new SplFileInfo($filePath);

        $file = new File($splFileInfo, $this->getConfig());

        $this->assertEquals($this->getFilePathname(), $file->getLocalPathname());

        $this->assertEquals(file_get_contents($filePath), $file->toGoogleVisionFile());
    }

    /** @test */
    public function it_should_read_accept_splFileObject(): void
    {
        $filePath = $this->getFilePathname();

        $splFileObject = new SplFileObject($filePath);

        $file = new File($splFileObject, $this->getConfig());

        $this->assertEquals($this->getFilePathname(), $file->getLocalPathname());

        $this->assertEquals(file_get_contents($filePath), $file->toGoogleVisionFile());
    }

    /** @test */
    public function it_should_accept_resource(): void
    {
        $resource = fopen($this->getFilePathname(), 'r');

        $config = $this->getConfig()->setTempDirPath($this->getTempDir());

        $file = new File($resource, $config);

        $this->assertFileExists($file->getLocalPathname());

        $this->assertIsResource($file->toGoogleVisionFile());

        $this->assertSame($resource, $file->toGoogleVisionFile());
    }

    /** @test */
    public function it_should_accept_google_storage_file(): void
    {
        $storage = 'gs://path/to/my/file.jpg';

        $file = new File($storage, $this->getConfig());

        $this->assertTrue($file->isGoogleStorage());

        $this->assertEquals($storage, $file->toGoogleVisionFile());
    }
}
