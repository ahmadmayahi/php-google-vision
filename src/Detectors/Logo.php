<?php

declare(strict_types=1);

namespace AhmadMayahi\Vision\Detectors;

use AhmadMayahi\Vision\Contracts\Detectable;
use AhmadMayahi\Vision\Data\Logo as LogoData;
use AhmadMayahi\Vision\Support\AbstractDetector;
use AhmadMayahi\Vision\Traits\Arrayable;
use AhmadMayahi\Vision\Traits\Jsonable;
use Generator;
use Google\Cloud\Vision\V1\EntityAnnotation;
use Google\Protobuf\Internal\RepeatedField;

/**
 * @see https://cloud.google.com/vision/docs/detecting-logos
 */
class Logo extends AbstractDetector implements Detectable
{
    use Arrayable;
    use Jsonable;

    public function getOriginalResponse(): RepeatedField
    {
        $response = $this
            ->imageAnnotatorClient
            ->logoDetection($this->file->toVisionFile());

        return $response->getLogoAnnotations();
    }

    public function detect(): ?Generator
    {
        $response = $this->getOriginalResponse();

        if (0 === $response->count()) {
            return null;
        }

        /** @var EntityAnnotation $item */
        foreach ($response as $item) {
            yield new LogoData($item->getDescription());
        }
    }
}
