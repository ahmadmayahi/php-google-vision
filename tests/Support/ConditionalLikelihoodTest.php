<?php

namespace AhmadMayahi\Vision\Tests\Support;

use AhmadMayahi\Vision\Enums\Likelihood;
use AhmadMayahi\Vision\Support\AbstractConditionalLikelihood;
use AhmadMayahi\Vision\Tests\TestCase;

final class ConditionalLikelihoodTest extends TestCase
{
    /** @test */
    public function it_should_get_conditional_methods(): void
    {
        $person = new Person('VERY_LIKELY', 'POSSIBLE');

        $this->assertTrue($person->isGood());
        $this->assertFalse($person->isBad());
    }

    /** @test */
    public function it_should_get_conditional_methods_with_custom_likelihood(): void
    {
        $person = new Person('VERY_LIKELY', 'POSSIBLE');

        $this->assertTrue($person->isBad(Likelihood::POSSIBLE));
    }

    /** @test */
    public function it_should_fail_if_method_does_not_start_with_is(): void
    {
        $person = new Person('VERY_LIKELY', 'POSSIBLE');

        $this->expectExceptionMessage('Conditional methods must start with "is"!');
        $this->assertEmpty($person->angry());
    }

    /** @test */
    public function it_should_fail_if_method_not_found(): void
    {
        $person = new Person('VERY_LIKELY', 'POSSIBLE');

        $this->expectExceptionMessage('Method not found!');
        $person->isCrazy();
    }
}

/**
 * @method bool isGood($likelihood = Likelihood::VERY_LIKELY)
 * @method bool isBad($likelihood = Likelihood::VERY_LIKELY)
 */
class Person extends AbstractConditionalLikelihood
{
    public function __construct(
        public string $good,
        public string $bad,
    ) {
    }

    protected function conditionals(): array
    {
        return [

        ];
    }
}
