<?php

namespace AhmadMayahi\Vision;

use AhmadMayahi\Vision\Utils\Container;

final class State
{
    public static Config $config;

    public static Container $container;

    public static function getContainer(): Container
    {
        return self::$container;
    }

    public static function getConfig(): Config
    {
        return self::$config;
    }
}
