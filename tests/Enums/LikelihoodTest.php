<?php

namespace AhmadMayahi\Vision\Tests\Enums;

use AhmadMayahi\Vision\Enums\Likelihood;
use AhmadMayahi\Vision\Tests\TestCase;
use Google\Cloud\Vision\V1\Likelihood as GoogleLikelihood;

final class LikelihoodTest extends TestCase
{
    /** @test */
    public function google_vision_likelihood()
    {
        $likelihood = [
            GoogleLikelihood::UNKNOWN => 'UNKNOWN',
            GoogleLikelihood::VERY_UNLIKELY => 'VERY_UNLIKELY',
            GoogleLikelihood::UNLIKELY => 'UNLIKELY',
            GoogleLikelihood::POSSIBLE => 'POSSIBLE',
            GoogleLikelihood::LIKELY => 'LIKELY',
            GoogleLikelihood::VERY_LIKELY => 'VERY_LIKELY',
        ];

        $this->assertEquals($likelihood, Likelihood::$likelihood);
    }

    /** @test */
    public function it_should_return_likelihood_value_from_key(): void
    {
        $this->assertEquals('UNKNOWN', Likelihood::fromKey(0));
    }

    /** @test */
    public function it_should_return_likelihood_key_from_value(): void
    {
        $this->assertEquals(0, Likelihood::fromVal('UNKNOWN'));
    }
}
