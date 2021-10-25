<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Data\ImageText as ImageTextData;
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
    public function getOriginalResponse(): ?RepeatedField
    {
        return $this->getDocumentTextAnnotations();
    }

    public function plain(): ?ImageTextData
    {
        $detection = $this
            ->imageAnnotatorClient
            ->textDetection($this->file->toVisionFile());

        $annotations = $detection->getTextAnnotations();

        if (! $annotations->count()) {
            return null;
        }

        /** @var EntityAnnotation $text */
        $text = $annotations->offsetGet(0);

        return new ImageTextData(
            locale: $text->getLocale(),
            text: $text->getDescription(),
        );
    }

    public function document(): ImageTextData
    {
        $text = $this->getDocumentTextAnnotations()->offsetGet(0);

        return new ImageTextData(
            locale: $text->getLocale(),
            text: $text->getDescription(),
        );
    }

    private function getDocumentTextAnnotations(): RepeatedField
    {
        return $this
            ->imageAnnotatorClient
            ->documentTextDetection($this->file->toVisionFile())
            ->getTextAnnotations();
    }

    public function __toString(): string
    {
        return $this->plain()?->text ?? '';
    }
}
