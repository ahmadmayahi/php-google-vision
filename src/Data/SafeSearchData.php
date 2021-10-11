<?php

namespace AhmadMayahi\Vision\Data;

/**
 * @see https://cloud.google.com/vision/docs/reference/rpc/google.cloud.vision.v1#safesearchannotation
 */
class SafeSearchData
{
    public function __construct(
        public string $adult,
        public string $medical,
        public string $spoof,
        public string $violence,
        public string $racy
    ) {
    }
}
