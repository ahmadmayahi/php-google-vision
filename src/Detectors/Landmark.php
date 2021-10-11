<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\LandmarkData;
use AhmadMayahi\Vision\Data\LocationData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use AhmadMayahi\Vision\Traits\Arrayable;
use AhmadMayahi\Vision\Traits\Jsonable;
use Generator;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Cloud\Vision\V1\LocationInfo;
use Google\Protobuf\Internal\RepeatedField;

/**
 * @see https://cloud.google.com/vision/docs/detecting-landmarks
 */
class Landmark extends AbstractDetector
{
    use Arrayable, Jsonable;

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->imageAnnotatorClient
            ->landmarkDetection($this->file->toVisionFile());

        return $response->getLandmarkAnnotations();
    }

    public function detect(): Generator
    {
        $response = $this->getOriginalResponse();

        if (0 === $response->count()) {
            return 0;
        }

        /** @var EntityAnnotation $entity */
        foreach ($response as $entity) {
            yield new LandmarkData(
                name: $entity->getDescription(),
                locations: array_map(function (LocationInfo $location) {
                    $info = $location->getLatLng();

                    if (is_null($info)) {
                        return null;
                    }

                    return new LocationData($info->getLatitude(), $info->getLongitude());
                }, iterator_to_array($entity->getLocations()->getIterator()))
            );
        }
    }
}
