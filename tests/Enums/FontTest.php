<?php

namespace AhmadMayahi\Vision\Tests\Enums;

use AhmadMayahi\Vision\Enums\Font;
use AhmadMayahi\Vision\Tests\TestCase;

final class FontTest extends TestCase
{
    /** @test */
    public function it_should_support_google_opensans_fonts(): void
    {
        $this->assertEquals(Font::OPEN_SANS_BOLD, 'OpenSans-Bold.ttf');

        $this->assertEquals(Font::OPEN_SANS_BOLD_ITALIC, 'OpenSans-BoldItalic.ttf');

        $this->assertEquals(Font::OPEN_SANS_EXTRA_BOLD_ITALIC, 'OpenSans-ExtraBoldItalic.ttf');

        $this->assertEquals(Font::OPEN_SANS_ITALIC, 'OpenSans-Italic.ttf');

        $this->assertEquals(Font::OPEN_SANS_LIGHT, 'OpenSans-Light.ttf');

        $this->assertEquals(Font::OPEN_SANS_LIGHT_ITALIC, 'OpenSans-LightItalic.ttf');

        $this->assertEquals(Font::OPEN_SANS_MEDIUM, 'OpenSans-Medium.ttf');

        $this->assertEquals(Font::OPEN_SANS_MEDIUM_ITALIC, 'OpenSans-MediumItalic.ttf');

        $this->assertEquals(Font::OPEN_SANS_MEDIUM_REGULAR, 'OpenSans-MediumRegular.ttf');

        $this->assertEquals(Font::OPEN_SANS_MEDIUM_SEMI_BOLD, 'OpenSans-SemiBold.ttf');
    }
}
