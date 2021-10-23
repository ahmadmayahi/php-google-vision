<?php

namespace AhmadMayahi\Vision\Enums;

use Google\Cloud\Vision\V1\Likelihood as GoogleLikelihood;

final class Likelihood
{
    public static array $likelihood = [
        GoogleLikelihood::UNKNOWN => 'UNKNOWN',
        GoogleLikelihood::VERY_UNLIKELY => 'VERY_UNLIKELY',
        GoogleLikelihood::UNLIKELY => 'UNLIKELY',
        GoogleLikelihood::POSSIBLE => 'POSSIBLE',
        GoogleLikelihood::LIKELY => 'LIKELY',
        GoogleLikelihood::VERY_LIKELY => 'VERY_LIKELY',
    ];

    public static function fromKey(int $key): ?string
    {
        return self::$likelihood[$key] ?? null;
    }

    public static function fromVal(string $val): ?int
    {
        if ($res = array_search($val, self::$likelihood)) {
            return intval($res);
        }

        return null;
    }
}
