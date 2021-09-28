<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\ImageTextData;
use AhmadMayahi\Vision\Traits\HasImageAnnotator;
use AhmadMayahi\Vision\Utils\AbstractDetector;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Protobuf\Internal\RepeatedField;

class Image extends AbstractDetector
{
    use HasImageAnnotator;

    public function getPlainText(): ?ImageTextData
    {
        $annotations = $this
            ->getImageAnnotaorClient()
            ->textDetection($this->file->toGoogleVisionFile())
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
            ->documentTextDetection($this->file->toGoogleVisionFile())
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
