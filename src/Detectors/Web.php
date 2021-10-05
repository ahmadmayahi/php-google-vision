<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\WebData;
use AhmadMayahi\Vision\Data\WebEntityData;
use AhmadMayahi\Vision\Data\WebImageData;
use AhmadMayahi\Vision\Data\WebPageData;
use AhmadMayahi\Vision\Utils\AbstractDetector;
use Google\Cloud\Vision\V1\WebDetection;
use Google\Cloud\Vision\V1\WebDetection\WebEntity;
use Google\Cloud\Vision\V1\WebDetection\WebImage;
use Google\Cloud\Vision\V1\WebDetection\WebLabel;
use Google\Cloud\Vision\V1\WebDetection\WebPage;

class Web extends AbstractDetector
{
    public function getOriginalResponse(): ?WebDetection
    {
        $web = $this->imageAnnotatorClient->webDetection($this->file->toGoogleVisionFile());

        return $web->getWebDetection();
    }

    public function detect(): ?WebData
    {
        $response = $this->getOriginalResponse();

        if (!$response) {
            return null;
        }

        $bestGuessLabels = $this->getBestGuessLabels($response);

        $pagesWithMatchingImages = $this->getPagesWithMatchingImages($response);

        $fullMatchingImages = $this->getFullMatchingImages($response);

        $partialMatchingImages = $this->getPartialMatchingImages($response);

        $visuallySimilarImages = $this->getVisuallySimilarImages($response);

        $webEntiries = $this->getWebEntiries($response);

        return new WebData(
            $bestGuessLabels,
            $pagesWithMatchingImages,
            $fullMatchingImages,
            $partialMatchingImages,
            $visuallySimilarImages,
            $webEntiries
        );
    }

    /**
     * @param WebDetection $response
     * @return array|string[]
     */
    public function getBestGuessLabels(WebDetection $response): array
    {
        return array_map(function (WebLabel $label) {
            return $label->getLabel();
        }, iterator_to_array($response->getBestGuessLabels()));
    }

    /**
     * @param WebDetection $response
     * @return WebPageData[]
     */
    public function getPagesWithMatchingImages(WebDetection $response): array
    {
        return array_map(function (WebPage $item) {
            return new WebPageData($item->getUrl(), $item->getPageTitle(), $item->getScore());
        }, iterator_to_array($response->getPagesWithMatchingImages()));
    }

    /**
     * @param WebDetection $response
     * @return WebImageData[]
     */
    public function getFullMatchingImages(WebDetection $response): array
    {
        return array_map(function (WebImage $item) {
            return new WebImageData($item->getUrl(), $item->getScore());
        }, iterator_to_array($response->getFullMatchingImages()));
    }

    /**
     * @param WebDetection $response
     * @return WebImageData[]
     */
    public function getPartialMatchingImages(WebDetection $response): array
    {
        return array_map(function (WebImage $item) {
            return new WebImageData($item->getUrl(), $item->getScore());
        }, iterator_to_array($response->getPartialMatchingImages()));
    }

    /**
     * @param WebDetection $response
     * @return WebImageData[]
     */
    public function getVisuallySimilarImages(WebDetection $response): array
    {
        return array_map(function (WebImage $item) {
            return new WebImageData($item->getUrl(), $item->getScore());
        }, iterator_to_array($response->getVisuallySimilarImages()));
    }

    /**
     * @param WebDetection $response
     * @return WebEntityData[]
     */
    public function getWebEntiries(WebDetection $response): array
    {
        return array_map(function (WebEntity $item) {
            return new WebEntityData($item->getEntityId(), $item->getScore(), $item->getDescription());
        }, iterator_to_array($response->getWebEntities()));
    }
}
