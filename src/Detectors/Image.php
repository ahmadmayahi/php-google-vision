<?php

namespace AhmadMayahi\GoogleVision\Detectors;

use AhmadMayahi\GoogleVision\Data\ImageTextData;
use AhmadMayahi\GoogleVision\Traits\HasImageAnnotator;
use AhmadMayahi\GoogleVision\Utils\AbstractExtractor;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Protobuf\Internal\RepeatedField;

class Image extends AbstractExtractor
{
    use HasImageAnnotator;

    public function getPlainText(): ?ImageTextData
    {
        $annotations = $this
            ->getImageAnnotaorClient()
            ->textDetection($this->file->getFileContents())
            ->getTextAnnotations();

        if (0 === $annotations->count()) {
            return null;
        }

        /** @var EntityAnnotation $text */
        $text = $annotations->offsetGet(0);

        return new ImageTextData(
            locale: $text->getLocale(),
            text: $text->getDescription(),
        );
    }

    /**
     * @return RepeatedField
     */
    public function getOriginalResponse(): RepeatedField
    {
        return $this->getTextAnnotations();
    }

    private function getTextAnnotations(): RepeatedField
    {
        return $this
            ->getImageAnnotaorClient()
            ->documentTextDetection($this->file->getFileContents())
            ->getTextAnnotations();
    }

    public function getDocument(): ImageTextData
    {
        $text = $this->getTextAnnotations()[0];

        return new ImageTextData(
            locale: $text->getLocale(),
            text: $text->getDescription(),
        );
    }

    public function __toString(): string
    {
        return $this->getPlainText()->getText();
    }
}
