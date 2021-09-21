<?php

namespace AhmadMayahi\GoogleVision\Detectors;

use AhmadMayahi\GoogleVision\Data\SafeSearchData;
use AhmadMayahi\GoogleVision\Enums\LikelihoodEnum;
use AhmadMayahi\GoogleVision\Traits\HasImageAnnotator;
use AhmadMayahi\GoogleVision\Utils\AbstractExtractor;
use Google\Cloud\Vision\V1\SafeSearchAnnotation;

class SafeSearch extends AbstractExtractor
{
    use HasImageAnnotator;

    public function getOriginalResponse(): ?SafeSearchAnnotation
    {
        $response = $this
            ->getImageAnnotaorClient()
            ->safeSearchDetection($this->file->getFileContents());

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
