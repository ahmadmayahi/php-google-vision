<?php

namespace AhmadMayahi\Vision\Tests\Traits;

use AhmadMayahi\Vision\Tests\TestCase;
use AhmadMayahi\Vision\Traits\Jsonable;
use Generator;

class JsonableTest extends TestCase
{
    /** @test */
    public function it_should_return_json_from_iterator(): void
    {
        $data = range(1, 10);

        $this->assertJson(json_encode($data), (new JsonData($data))->asJson());
    }

    /** @test */
    public function it_should_return_empty_json_if_iterator_returns_is_empty(): void
    {
        $this->assertJson(json_encode([]), (new JsonData([]))->asJson());
    }

}

class JsonData
{
    use Jsonable;

    public function __construct(private array $data)
    {
    }

    public function detect(): Generator
    {
        foreach ($this->data as $item) {
            yield $item;
        }
    }
}
