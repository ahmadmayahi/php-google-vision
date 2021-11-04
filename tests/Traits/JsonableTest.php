<?php

namespace AhmadMayahi\Vision\Tests\Traits;

use AhmadMayahi\Vision\Tests\TestCase;
use AhmadMayahi\Vision\Traits\Jsonable;

class JsonableTest extends TestCase
{
    /** @test */
    public function it_should_return_json_from_iterator(): void
    {
        $this->assertSame(json_encode(range(1, 10)), (new Data())->asJson());
    }
}

class Data
{
    use Jsonable;

    public function detect(): mixed
    {
        foreach (range(1, 10) as $item) {
            yield $item;
        }
    }
}
