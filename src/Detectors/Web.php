<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\Detectable;
use AhmadMayahi\Vision\Data\Web as WebData;
use AhmadMayahi\Vision\Data\WebImage as WebImageData;
use AhmadMayahi\Vision\Data\WebPage as WebPageData;
use AhmadMayahi\Vision\Data\WebEntity as WebEntityData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use Google\Cloud\Vision\V1\WebDetection;
use Google\Cloud\Vision\V1\WebDetection\WebEntity;
use Google\Cloud\Vision\V1\WebDetection\WebImage;
use Google\Cloud\Vision\V1\WebDetection\WebLabel;
use Google\Cloud\Vision\V1\WebDetection\WebPage;

class Web extends AbstractDetector implements Detectable
{
    public function getOriginalResponse(): ?WebDetection
    {
        $web = $this
            ->imageAnnotatorClient
            ->webDetection($this->file->toVisionFile());

        return $web->getWebDetection();
    }

    public function detect(): ?WebData
    {
        $response = $this->getOriginalResponse();

        if (! $response) {
            return null;
        }

        return new WebData(
            $this->getBestGuessLabels($response),
            $this->getPagesWithMatchingImages($response),
            $this->getFullMatchingImages($response),
            $this->getPartialMatchingImages($response),
            $this->getVisuallySimilarImages($response),
            $this->getWebEntries($response)
        );
    }

    /**
     * @param WebDetection $response
     * @return array|string[]
     */
    protected function getBestGuessLabels(WebDetection $response): array
    {
        return array_map(function (WebLabel $label) {
            return $label->getLabel();
        }, iterator_to_array($response->getBestGuessLabels()));
    }

    /**
     * @param WebDetection $response
     * @return WebPageData[]
     */
    protected function getPagesWithMatchingImages(WebDetection $response): array
    {
        return array_map(function (WebPage $item) {
            return new WebPageData($item->getUrl(), $item->getPageTitle(), $item->getScore());
        }, iterator_to_array($response->getPagesWithMatchingImages()));
    }

    /**
     * @param WebDetection $response
     * @return WebImageData[]
     */
    protected function getFullMatchingImages(WebDetection $response): array
    {
        return array_map(function (WebImage $item) {
            return new WebImageData($item->getUrl(), $item->getScore());
        }, iterator_to_array($response->getFullMatchingImages()));
    }

    /**
     * @param WebDetection $response
     * @return WebImageData[]
     */
    protected function getPartialMatchingImages(WebDetection $response): array
    {
        return array_map(function (WebImage $item) {
            return new WebImageData($item->getUrl(), $item->getScore());
        }, iterator_to_array($response->getPartialMatchingImages()));
    }

    /**
     * @param WebDetection $response
     * @return WebImageData[]
     */
    protected function getVisuallySimilarImages(WebDetection $response): array
    {
        return array_map(function (WebImage $item) {
            return new WebImageData($item->getUrl(), $item->getScore());
        }, iterator_to_array($response->getVisuallySimilarImages()));
    }

    /**
     * @param WebDetection $response
     * @return WebEntityData[]
     */
    protected function getWebEntries(WebDetection $response): array
    {
        return array_map(function (WebEntity $item) {
            return new WebEntityData($item->getEntityId(), $item->getScore(), $item->getDescription());
        }, iterator_to_array($response->getWebEntities()));
    }
}
