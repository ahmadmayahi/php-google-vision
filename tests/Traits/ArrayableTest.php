<?php

namespace AhmadMayahi\Vision\Tests\Traits;

use AhmadMayahi\Vision\Tests\TestCase;
use AhmadMayahi\Vision\Traits\Arrayable;

class ArrayableTest extends TestCase
{
    /** @test */
    public function it_should_return_array_from_iterator(): void
    {
        $this->assertSame(range(1, 10), (new Data())->asArray());
    }
}

class Data
{
    use Arrayable;

    public function detect(): mixed
    {
        foreach (range(1, 10) as $item) {
            yield $item;
        }
    }
}
