<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\Detectable;
use AhmadMayahi\Vision\Contracts\File;
use AhmadMayahi\Vision\Data\Face as FaceData;
use AhmadMayahi\Vision\Data\Vertex as VertexData;
use AhmadMayahi\Vision\Detectors\Face\DrawBoxAroundFaces;
use AhmadMayahi\Vision\Enums\Color;
use AhmadMayahi\Vision\Enums\Likelihood;
use AhmadMayahi\Vision\Support\AbstractDetector;
use AhmadMayahi\Vision\Support\Image;
use AhmadMayahi\Vision\Traits\Arrayable;
use AhmadMayahi\Vision\Traits\Jsonable;
use Generator;
use Google\Cloud\Vision\V1\FaceAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Vertex;
use Google\Protobuf\Internal\RepeatedField;

/**
 * Face Detection detects multiple faces within an image along with the associated key facial attributes
 * such as emotional state or wearing headwear.
 *
 * @see https://cloud.google.com/vision/docs/detecting-faces
 */
class Face extends AbstractDetector implements Detectable
{
    use Arrayable;
    use Jsonable;

    public function __construct(protected ImageAnnotatorClient $imageAnnotatorClient, protected File $file, protected Image $image)
    {
    }

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->imageAnnotatorClient
            ->faceDetection($this->file->toVisionFile());

        return $response->getFaceAnnotations();
    }

    public function detect(): ?Generator
    {
        $faces = $this->getOriginalResponse();
        if (0 === $faces->count()) {
            return null;
        }

        /** @var FaceAnnotation $face */
        foreach ($faces as $face) {
            $vertices = $face->getBoundingPoly()->getVertices();

            $bounds = array_map(function (Vertex $vertex) {
                return new VertexData($vertex->getX(), $vertex->getY());
            }, iterator_to_array($vertices));

            yield new FaceData(
                anger: Likelihood::fromKey($face->getAngerLikelihood()),
                joy: Likelihood::fromKey($face->getJoyLikelihood()),
                surprise: Likelihood::fromKey($face->getSurpriseLikelihood()),
                blurred: Likelihood::fromKey($face->getBlurredLikelihood()),
                headwear: Likelihood::fromKey($face->getHeadwearLikelihood()),
                underExposes: Likelihood::fromKey($face->getUnderExposedLikelihood()),
                bounds: $bounds,
                detectionConfidence: $face->getDetectionConfidence(),
                landmarking: $face->getLandmarkingConfidence(),
            );
        }
    }

    public function drawBoxAroundFaces(int $borderColor = Color::GREEN): Image
    {
        return (new DrawBoxAroundFaces($this, $this->image))
            ->setBorderColor($borderColor)
            ->draw();
    }
}
