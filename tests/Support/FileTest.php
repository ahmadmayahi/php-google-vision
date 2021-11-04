<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Tests\Support;

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

        $file = new File($resource, $this->getTempDir());

        $this->assertTrue($file->isResource());

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

    /** @test */
    public function it_should_not_accept_google_storage_in_getLocalPath(): void
    {
        $this->expectExceptionMessage('Google Storage is not supported for this operation!');

        $storage = 'gs://path/to/my/file.jpg';

        $file = new File($storage, $this->getTempDir());

        $file->getLocalPathname();
    }

    /** @test */
    public function it_should_not_accept_google_storage_in_getContents(): void
    {
        $this->expectExceptionMessage('Google Storage is not supported');

        $storage = 'gs://path/to/my/file.jpg';

        $file = new File($storage, $this->getTempDir());

        $file->getContents();
    }

    /** @test */
    public function it_should_fail_if_file_not_compatible_with_google_vision(): void
    {
        $this->expectExceptionMessage('File not found or not compatible');

        $file = new File('myfile', $this->getTempDir());

        $file->toVisionFile();
    }

    /** @test */
    public function it_should_fail_if_cant_get_local_file_path(): void
    {
        $this->expectExceptionMessage('Cannot get the local file path');

        $file = new File('myfile', $this->getTempDir());

        $file->getLocalPathname();
    }

    /** @test */
    public function it_should_return_file_path_from_resource(): void
    {
        /** @var resource $stream */
        $stream = fopen($this->getFilePathname(), 'r');

        $file = new File($stream, $this->getTempDir());

        $this->assertFileExists($file->getStreamFilePath());

        $this->assertFileExists($file->getLocalPathname());
    }

    /** @test */
    public function it_should_fail_if_the_given_file_is_not_resource(): void
    {
        $this->expectExceptionMessage('File is not resource');

        $file = new File('xx', $this->getTempDir());

        $file->getStreamFilePath();
    }

    /** @test */
    public function it_should_return_resource_contents(): void
    {
        /** @var resource $stream */
        $stream = fopen($this->getFilePathname(), 'r');

        $file = new File($stream, $this->getTempDir());

        $this->assertSame(file_get_contents($this->getFilePathname()), $file->getContents());
    }

    /** @test */
    public function it_should_return_spl_file_info_contents(): void
    {
        /** @var resource $stream */
        $stream = new SplFileInfo($this->getFilePathname());

        $file = new File($stream, $this->getTempDir());

        $this->assertSame(file_get_contents($this->getFilePathname()), $file->getContents());
    }
}
