<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\Detectable;
use AhmadMayahi\Vision\Data\SafeSearch as SafeSearchData;
use AhmadMayahi\Vision\Enums\Likelihood;
use AhmadMayahi\Vision\Support\AbstractDetector;
use Google\Cloud\Vision\V1\SafeSearchAnnotation;

class SafeSearch extends AbstractDetector implements Detectable
{
    public function getOriginalResponse(): ?SafeSearchAnnotation
    {
        $response = $this
            ->imageAnnotatorClient
            ->safeSearchDetection($this->file->toVisionFile());

        return $response->getSafeSearchAnnotation();
    }

    public function detect(): ?SafeSearchData
    {
        $response = $this->getOriginalResponse();

        if (! $response) {
            return null;
        }

        return (new SafeSearchData(
            adult: Likelihood::fromKey($response->getAdult()),
            medical: Likelihood::fromKey($response->getMedical()),
            spoof: Likelihood::fromKey($response->getSpoof()),
            violence: Likelihood::fromKey($response->getViolence()),
            racy: Likelihood::fromKey($response->getRacy()),
        ));
    }
}
