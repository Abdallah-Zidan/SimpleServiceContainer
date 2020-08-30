<?php


namespace Container\Classes;


use Container\Interfaces\ContainerInterface;

class NormalProvider extends SimpleServiceProvider
{
    protected $autoWireHelper;
    protected $tempServices;

    /**
     * @param array $services
     * @param bool $isAutoWired
     * @param AutoWireHelper|null $autoWireHelper
     */

    public function __construct(array $services,$isAutoWired = false, AutoWireHelper $autoWireHelper = null)
    {
        parent::__construct();

        $this->autoWireHelper = $autoWireHelper;

        $this->isAutoWired = $isAutoWired;

        $this->tempServices =$services;


    }

    /**
     * @param ContainerInterface $container
     */
    public function register(ContainerInterface $container): void
    {
        if(!$this->autoWireHelper){
            $this->isAutoWired = false;
        }

        $this->addServices($this->tempServices);

        $this->tempServices = array();

        parent::register($container);
    }
}