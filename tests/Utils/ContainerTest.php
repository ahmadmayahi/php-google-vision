<?php

namespace AhmadMayahi\GoogleVision\Tests\Utils;

use AhmadMayahi\GoogleVision\Tests\TestCase;
use AhmadMayahi\GoogleVision\Utils\Container;

final class ContainerTest extends TestCase
{
    /** @test */
    public function it_should_get_same_container_instance(): void
    {
        $container1 = Container::getInstance();
        $container2 = Container::getInstance();

        $this->assertSame($container1, $container2);
    }

    /** @test */
    public function it_should_bind_a_class_by_name(): void
    {
        $class = new Class {

        };

        Container::getInstance()->bind($class);

        $this->assertTrue(Container::getInstance()->has($class::class));
    }

    /** @test */
    public function it_should_bind_an_object()
    {
        $class = new class {
            public function say(): string
            {
                return 'Hello World';
            }
        };

        Container::getInstance()->bind($class);

        $resolvedClass = Container::getInstance()->get($class::class);

        $this->assertInstanceOf($resolvedClass::class, $class);

        $this->assertEquals('Hello World', $resolvedClass->say());
    }

    /** @test */
    public function it_should_resolve_object_with_arguments_in_constructor()
    {
        $class = new WelcomeClass('Ahmad');

        Container::getInstance()->bind($class);

        $this->assertTrue(Container::getInstance()->has($class::class));

        $resolvedClass = Container::getInstance()->get($class::class, 'Ahmad');

        $this->assertEquals('Welcome Ahmad', $resolvedClass->say());
    }
}

class WelcomeClass
{
    public function __construct(private string $name)
    {

    }

    public function say(): string
    {
        return 'Welcome ' . $this->name;
    }
}
