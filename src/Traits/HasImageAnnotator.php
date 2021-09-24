<?php

namespace AhmadMayahi\GoogleVision\Traits;

use AhmadMayahi\GoogleVision\State;
use AhmadMayahi\GoogleVision\Utils\Container;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

trait HasImageAnnotator
{
    /**
     * @throws \Google\ApiCore\ValidationException
     */
    protected function registerDependencies(): void
    {
        Container::getInstance()->bindOnce(new ImageAnnotatorClient(State::$config->connectConfig()));
    }

    protected function getImageAnnotaorClient(): ImageAnnotatorClient
    {
        return Container::getInstance()->get(ImageAnnotatorClient::class);
    }

    public function __destruct()
    {
        $this->getImageAnnotaorClient()->close();
    }
}
