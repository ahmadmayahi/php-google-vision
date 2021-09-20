<?php

namespace AhmadMayahi\GoogleVision\Utils;

use InvalidArgumentException;
use ReflectionClass;

class Container
{
    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    private static ?Container $instance = null;

    /**
     * The container's bindings.
     *
     * @var array array[]
     */
    protected array $bindings = [];

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance(): static
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->bindings);
    }

    public function bind(string|object $object, string $name = null): void
    {
        if (is_string($object)) {
            $name = $object;
        }

        $this->bindings[$name ?? $object::class] = $object;
    }

    public function bindOnce(string|object $object, string $name = null): void
    {
        if (is_object($object) && $this->has($object::class)) {
            return ;
        }

        if (is_string($object) && $this->has($object)) {
            return ;
        }

        if (null !== $name && $this->has($name)) {
            return ;
        }

        $this->bind($object, $name);
    }

    public function get(string $name, mixed ...$args): mixed
    {
        if (false === array_key_exists($name, $this->bindings)) {
            throw new InvalidArgumentException('Object '.$name.' does not exist in the container');
        }

        $class = $this->bindings[$name];

        if (false === is_object($class)) {
            if ($args) {
                $reflectionClass = new ReflectionClass($class);

                return $reflectionClass->newInstanceArgs($args);
            }

            return new $class();
        }

        return $class;
    }

    public function all(): array
    {
        return $this->bindings;
    }
}
