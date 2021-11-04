<?php

namespace AhmadMayahi\Vision\Tests\Traits;

use AhmadMayahi\Vision\Tests\TestCase;
use AhmadMayahi\Vision\Traits\Arrayable;
use Generator;

class ArrayableTest extends TestCase
{
    /** @test */
    public function it_should_return_array_from_iterator(): void
    {
        $data = range(1, 10);

        $this->assertSame($data, (new ArrayData($data))->asArray());
    }

    /** @test */
    public function it_should_return_empty_array_if_iterator_returns_is_empty(): void
    {
        $this->assertEmpty((new ArrayData([]))->asArray());
    }
}

class ArrayData
{
    use Arrayable;

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
