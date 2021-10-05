<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\SafeSearchData;
use AhmadMayahi\Vision\Enums\LikelihoodEnum;
use AhmadMayahi\Vision\Support\AbstractDetector;
use Google\Cloud\Vision\V1\SafeSearchAnnotation;

class SafeSearch extends AbstractDetector
{
    public function getOriginalResponse(): ?SafeSearchAnnotation
    {
        $response = $this
            ->imageAnnotatorClient
            ->safeSearchDetection($this->file->toGoogleVisionFile());

        return $response->getSafeSearchAnnotation();
    }

    public function detect(): SafeSearchData
    {
        $response = $this->getOriginalResponse();

        return (new SafeSearchData(
            adult: LikelihoodEnum::fromKey($response->getAdult()),
            medical: LikelihoodEnum::fromKey($response->getMedical()),
            spoof: LikelihoodEnum::fromKey($response->getSpoof()),
            violence: LikelihoodEnum::fromKey($response->getViolence()),
            racy: LikelihoodEnum::fromKey($response->getRacy()),
        ));
    }
}
