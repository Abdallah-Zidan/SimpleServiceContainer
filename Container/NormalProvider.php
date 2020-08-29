<?php

namespace Container;

class NormalProvider extends AbstractProvider
{
    public static function getInstance(array $services = array() ): AbstractProvider
    {
        if (!self::$_instance) {
            self::$_instance = new self();
            self::$_instance->setServices($services);
        }
        return self::$_instance;
    }
}
