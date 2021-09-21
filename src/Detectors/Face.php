<?php

namespace AhmadMayahi\GoogleVision\Detectors;

use AhmadMayahi\GoogleVision\Contracts\File;
use AhmadMayahi\GoogleVision\Data\FaceData;
use AhmadMayahi\GoogleVision\Enums\ColorEnum;
use AhmadMayahi\GoogleVision\Enums\LikelihoodEnum;
use AhmadMayahi\GoogleVision\Traits\HasImageAnnotator;
use AhmadMayahi\GoogleVision\Utils\AbstractExtractor;
use AhmadMayahi\GoogleVision\Utils\Container;
use AhmadMayahi\GoogleVision\Utils\DrawBoxImage;
use Exception;
use Google\Cloud\Vision\V1\FaceAnnotation;
use Google\Cloud\Vision\V1\Vertex;
use Google\Protobuf\Internal\RepeatedField;

class Face extends AbstractExtractor
{
    use HasImageAnnotator;

    public function __construct(protected File $file)
    {
        parent::__construct($this->file);

        Container::getInstance()->bindOnce(DrawBoxImage::class);
    }

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->getImageAnnotaorClient()
            ->faceDetection($this->file->getFileContents());

        return $response->getFaceAnnotations();
    }

    /**
     * @return FaceData[]
     */
    public function detect(): array
    {
        $faces = $this->getOriginalResponse();

        $results = [];

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

            $results[] = new FaceData(
                anger: LikelihoodEnum::fromKey($anger),
                joy: LikelihoodEnum::fromKey($joy),
                surprise: LikelihoodEnum::fromKey($surprise),
                bounds: $bounds,
            );
        }

        return $results;
    }

    public function drawBoxAroundFaces(string $outFile, int $color = ColorEnum::GREEN)
    {
        $faces = $this->getOriginalResponse();

        $path = $this->file->getPathname();

        if (false === copy($path, $outFile)) {
            throw new Exception('Could not copy the file');
        }

        /** @var DrawBoxImage $outputImage */
        $outputImage = Container::getInstance()->get(DrawBoxImage::class, $outFile);

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

        $outputImage->saveImage($outFile);
    }
}
