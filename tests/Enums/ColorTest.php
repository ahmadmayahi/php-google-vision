<?php

namespace AhmadMayahi\Vision\Tests\Enums;

use AhmadMayahi\Vision\Enums\Color;
use AhmadMayahi\Vision\Tests\TestCase;

final class ColorTest extends TestCase
{
    /** @test  */
    public function black_color(): void
    {
        $this->assertEquals(Color::BLACK, 0x000000);
    }

    /** @test  */
    public function white_color(): void
    {
        $this->assertEquals(Color::WHITE, 0xffffff);
    }

    /** @test  */
    public function red_color(): void
    {
        $this->assertEquals(Color::RED, 0xff0000);
    }

    /** @test  */
    public function green_color(): void
    {
        $this->assertEquals(Color::GREEN, 0x00ff00);
    }

    /** @test  */
    public function blue_color(): void
    {
        $this->assertEquals(Color::BLUE, 0x0000ff);
    }

    /** @test  */
    public function yellow_color(): void
    {
        $this->assertEquals(Color::YELLOW, 0xffff00);
    }

    /** @test  */
    public function cyan_color(): void
    {
        $this->assertEquals(Color::CYAN, 0x00ffff);
    }

    /** @test  */
    public function magenta_color(): void
    {
        $this->assertEquals(Color::MAGENTA, 0xff00ff);
    }
}
