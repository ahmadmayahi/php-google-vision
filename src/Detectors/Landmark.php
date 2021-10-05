<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\LandmarkData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use Generator;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\LocationInfo;
use Google\Protobuf\Internal\RepeatedField;

/**
 * @see https://cloud.google.com/vision/docs/detecting-landmarks
 */
class Landmark extends AbstractDetector
{
    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->imageAnnotatorClient
            ->landmarkDetection($this->file->toGoogleVisionFile());

        return $response->getLandmarkAnnotations();
    }

    public function detect(): Generator
    {
        $response = $this->getOriginalResponse();

        /** @var EntityAnnotation $entity */
        foreach ($response as $entity) {
            yield new LandmarkData(
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
    }
}
