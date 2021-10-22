<?php

namespace AhmadMayahi\Vision\Enums;

use Google\Cloud\Vision\V1\Likelihood;

final class LikelihoodEnum
{
    public static array $likelihood = [
        Likelihood::UNKNOWN => 'UNKNOWN',
        Likelihood::VERY_UNLIKELY => 'VERY_UNLIKELY',
        Likelihood::UNLIKELY => 'UNLIKELY',
        Likelihood::POSSIBLE => 'POSSIBLE',
        Likelihood::LIKELY => 'LIKELY',
        Likelihood::VERY_LIKELY => 'VERY_LIKELY',
    ];

    public static function fromKey(int $key): ?string
    {
        return self::$likelihood[$key] ?? null;
    }

    public static function fromVal(string $val): ?int
    {
        if ($res = array_search($val, self::$likelihood)) {
            return $res;
        }

        return null;
    }
}
