<?php

namespace Container;

class NormalProvider extends AbstractProvider
{
    /**
     * NormalProvider constructor.
     * @param array $services
     */
    public function __construct(array $services = array())
    {
        parent::__construct();
        $this->setServices($services);
    }

}
