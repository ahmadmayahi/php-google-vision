<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\LandmarkData;
use AhmadMayahi\Vision\Traits\HasImageAnnotator;
use AhmadMayahi\Vision\Utils\AbstractDetector;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\LocationInfo;
use Google\Protobuf\Internal\RepeatedField;

/**
 * @see https://cloud.google.com/vision/docs/detecting-landmarks
 */
class Landmark extends AbstractDetector
{
    use HasImageAnnotator;

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->getImageAnnotaorClient()
            ->landmarkDetection($this->file->toGoogleVisionFile());

        return $response->getLandmarkAnnotations();
    }

    /**
     * @return LandmarkData[]
     */
    public function detect(): array
    {
        $response = $this->getOriginalResponse();

        $results = [];

        /** @var EntityAnnotation $entity */
        foreach ($response as $entity) {
            $results[] = new LandmarkData(
                name: $entity->getDescription(),
                locations: array_map(function (LocationInfo $location) {
                    $info = $location->getLatLng();

                    return [
                        'latitude' => $info->getLatitude(),
                        'longitude' => $info->getLongitude(),
                    ];
                }, iterator_to_array($entity->getLocations()->getIterator()))
            );
        }

        return $results;
    }
}
