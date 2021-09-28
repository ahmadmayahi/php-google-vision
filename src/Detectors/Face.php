<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\File;
use AhmadMayahi\Vision\Data\FaceData;
use AhmadMayahi\Vision\Enums\ColorEnum;
use AhmadMayahi\Vision\Enums\LikelihoodEnum;
use AhmadMayahi\Vision\Traits\HasImageAnnotator;
use AhmadMayahi\Vision\Utils\AbstractDetector;
use AhmadMayahi\Vision\Utils\Container;
use AhmadMayahi\Vision\Utils\Image;
use Exception;
use Generator;
use Google\Cloud\Vision\V1\FaceAnnotation;
use Google\Cloud\Vision\V1\Vertex;
use Google\Protobuf\Internal\RepeatedField;

class Face extends AbstractDetector
{
    use HasImageAnnotator;

    public function __construct(protected File $file, private ?string $outputFile = null)
    {
        parent::__construct($this->file);

        Container::getInstance()->bindOnce(Image::class);
    }

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->getImageAnnotaorClient()
            ->faceDetection($this->file->toGoogleVisionFile());

        return $response->getFaceAnnotations();
    }

    public function detect(): Generator
    {
        $faces = $this->getOriginalResponse();

        /** @var FaceAnnotation $face */
        foreach ($faces as $face) {
            $anger = $face->getAngerLikelihood();

            $joy = $face->getJoyLikelihood();
            $surprise = $face->getSurpriseLikelihood();

            $vertices = $face->getBoundingPoly()->getVertices();
            $bounds = [];

            /** @var Vertex $vertex */
            foreach ($vertices as $vertex) {
                $bounds[] = [
                    'x' => $vertex->getX(),
                    'y' => $vertex->getY(),
                ];
            }

            yield new FaceData(
                anger: LikelihoodEnum::fromKey($anger),
                joy: LikelihoodEnum::fromKey($joy),
                surprise: LikelihoodEnum::fromKey($surprise),
                bounds: $bounds,
            );
        }
    }

    public function drawBoxAroundFaces(int $color = ColorEnum::GREEN)
    {
        $faces = $this->getOriginalResponse();

        $path = $this->file->getLocalPathname();

        if (false === copy($path, $this->outputFile)) {
            throw new Exception('Could not copy the file');
        }

        /** @var Image $outputImage */
        $outputImage = Container::getInstance()->get(Image::class, $this->outputFile);

        /** @var FaceAnnotation $face */
        foreach ($faces as $face) {
            $vertices = $face->getBoundingPoly()->getVertices();

            if ($vertices) {
                $x1 = $vertices[0]->getX();
                $y1 = $vertices[0]->getY();

                $x2 = $vertices[2]->getX();
                $y2 = $vertices[2]->getY();

                $outputImage->drawRectangle($x1, $y1, $x2, $y2, $color);
            }
        }

        $outputImage->saveImage($this->outputFile);
    }
}
