<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Support;

use AhmadMayahi\Vision\Enums\Likelihood;
use Exception;

abstract class AbstractConditionalLikelihood
{
    public function __call(string $name, array $arguments)
    {
        if (false === str_starts_with($name, 'is')) {
            throw new Exception('Conditional methods must start with "is"!');
        }

        $property = lcfirst(substr($name, 2));

        $property = $this->conditionals()[$property] ?? $property;

        if (false === property_exists($this, $property)) {
            throw new Exception('Method not found!');
        }

        $likelihood = $arguments[0] ?? Likelihood::VERY_LIKELY;

        return $this->{$property} === Likelihood::fromKey($likelihood);
    }

    abstract protected function conditionals(): array;
}
