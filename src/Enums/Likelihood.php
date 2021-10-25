<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Enums;

use Google\Cloud\Vision\V1\Likelihood as LikelihoodVision;

class Likelihood
{
    /**
     * Unknown likelihood.
     *
     * Generated from protobuf enum <code>UNKNOWN = 0;</code>
     */
    public const UNKNOWN = LikelihoodVision::UNKNOWN;

    /**
     * It is very unlikely.
     *
     * Generated from protobuf enum <code>VERY_UNLIKELY = 1;</code>
     */
    public const VERY_UNLIKELY = LikelihoodVision::VERY_UNLIKELY;

    /**
     * It is unlikely.
     *
     * Generated from protobuf enum <code>UNLIKELY = 2;</code>
     */
    public const UNLIKELY = LikelihoodVision::UNLIKELY;

    /**
     * It is possible.
     *
     * Generated from protobuf enum <code>POSSIBLE = 3;</code>
     */

    public const POSSIBLE = LikelihoodVision::POSSIBLE;
    /**
     * It is likely.
     *
     * Generated from protobuf enum <code>LIKELY = 4;</code>
     */

    public const LIKELY = LikelihoodVision::LIKELY;
    /**
     * It is very likely.
     *
     * Generated from protobuf enum <code>VERY_LIKELY = 5;</code>
     */
    public const VERY_LIKELY = LikelihoodVision::VERY_LIKELY;

    /**
     * @var array|string[]
     */
    public static array $likelihood = [
        self::UNKNOWN => 'UNKNOWN',
        self::VERY_UNLIKELY => 'VERY_UNLIKELY',
        self::UNLIKELY => 'UNLIKELY',
        self::POSSIBLE => 'POSSIBLE',
        self::LIKELY => 'LIKELY',
        self::VERY_LIKELY => 'VERY_LIKELY',
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
