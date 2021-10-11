<?php

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\ImageTextData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Protobuf\Internal\RepeatedField;

/**
 * Detect text in images
 *
 * @see https://cloud.google.com/vision/docs/ocr
 */
class ImageText extends AbstractDetector
{
    public function plain(): ?ImageTextData
    {
        $annotations = $this
            ->imageAnnotatorClient
            ->textDetection($this->file->toVisionFile())
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
     * @return RepeatedField|null
     */
    public function getOriginalResponse(): ?RepeatedField
    {
        return $this->getTextAnnotations();
    }

    private function getTextAnnotations(): RepeatedField
    {
        return $this
            ->imageAnnotatorClient
            ->documentTextDetection($this->file->toVisionFile())
            ->getTextAnnotations();
    }

    public function document(): ImageTextData
    {
        $text = $this->getTextAnnotations()->offsetGet(0);

        return new ImageTextData(
            locale: $text->getLocale(),
            text: $text->getDescription(),
        );
    }

    public function __toString(): string
    {
        return $this->plain()?->text ?? '';
    }
}
