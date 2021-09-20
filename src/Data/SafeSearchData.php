<?php

namespace AhmadMayahi\GoogleVision\Data;

/**
 * @see https://cloud.google.com/vision/docs/reference/rpc/google.cloud.vision.v1#safesearchannotation
 */
class SafeSearchData
{
    public function __construct(
        private string $adult,
        private string $medical,
        private string $spoof,
        private string $violence,
        private string $racy
    ) {
    }

    /**
     * Represents the adult content likelihood for the image.
     * Adult content may contain elements such as nudity, pornographic images or cartoons, or sexual activities.
     *
     * @return string
     */
    public function getAdult(): string
    {
        return $this->adult;
    }

    /**
     * Likelihood that this is a medical image.
     *
     * @return string
     */
    public function getMedical(): string
    {
        return $this->medical;
    }

    /**
     * The likelihood that an modification was made to the image's canonical version to make it appear funny or offensive.
     *
     * @return string
     */
    public function getSpoof(): string
    {
        return $this->spoof;
    }

    /**
     * Likelihood that this image contains violent content.
     *
     * @return string
     */
    public function getViolence(): string
    {
        return $this->violence;
    }

    /**
     * Likelihood that the request image contains racy content.
     * Racy content may include (but is not limited to) skimpy or sheer clothing, strategically covered nudity,
     * lewd or provocative poses, or close-ups of sensitive body areas.
     *
     * @return string
     */
    public function getRacy(): string
    {
        return $this->racy;
    }
}
