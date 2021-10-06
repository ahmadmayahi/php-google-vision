<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\File;
use AhmadMayahi\Vision\Data\FaceData;
use AhmadMayahi\Vision\Data\VertexData;
use AhmadMayahi\Vision\Detectors\Face\DrawBoxAroundFaces;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Enums\LikelihoodEnum;
use AhmadMayahi\Vision\Support\AbstractDetector;
use AhmadMayahi\Vision\Support\Image;
use Generator;
use Google\Cloud\Vision\V1\FaceAnnotation;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Vertex;
use Google\Protobuf\Internal\RepeatedField;

class Face extends AbstractDetector
{
    public function __construct(protected ImageAnnotatorClient $imageAnnotatorClient, protected File $file, protected Image $image)
    {
    }

    public function getOriginalResponse(): ?RepeatedField
    {
        $response = $this
            ->imageAnnotatorClient
            ->faceDetection($this->file->toGoogleVisionFile());

        return $response->getFaceAnnotations();
    }

    public function detect(): Generator
    {
        $faces = $this->getOriginalResponse();

        if (! $faces) {
            return null;
        }

        /** @var FaceAnnotation $face */
        foreach ($faces as $face) {
            $anger = $face->getAngerLikelihood();

            $joy = $face->getJoyLikelihood();
            $surprise = $face->getSurpriseLikelihood();

            $vertices = $face->getBoundingPoly()->getVertices();
            $bounds = [];

            /** @var Vertex $vertex */
            foreach ($vertices as $vertex) {
                $bounds[] = new VertexData($vertex->getX(), $vertex->getY());
            }

            yield new FaceData(
                anger: LikelihoodEnum::fromKey($anger),
                joy: LikelihoodEnum::fromKey($joy),
                surprise: LikelihoodEnum::fromKey($surprise),
                bounds: $bounds,
            );
        }
    }

    public function drawBoxAroundFaces(int $borderColor = ColorEnum::GREEN): Image
    {
        return (new DrawBoxAroundFaces($this, $this->image))
            ->setBorderColor($borderColor)
            ->draw();
    }
}
