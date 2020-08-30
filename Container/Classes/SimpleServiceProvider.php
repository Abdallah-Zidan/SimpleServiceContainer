<?php


namespace Container\Classes;


use Container\Interfaces\ContainerInterface;
use Container\Interfaces\ProviderInterface;


abstract class SimpleServiceProvider implements ProviderInterface
{
    protected $services;
    protected $isAutoWired;
    protected $autoWireHelper;

    public function __construct()
    {
        $this->services = array();
        $this->isAutoWired = false;
        $this->autoWireHelper = null;
    }


    /**
     * @param array $services
     */
    public function addServices(array $services): void
    {

        if ($this->isAutoWired) {
            $this->addServicesRecursively($services, $this->autoWireHelper);
        } else {
            $this->setServicesNormally($services);
        }

    }

    /**
     * @param array $services
     * @param AutoWireHelper $autoWireHelper
     */
    protected function addServicesRecursively(array $services, AutoWireHelper $autoWireHelper): void
    {
        $this->services = array();

        foreach ($services as $service) {
            $this->addServiceRecursively($service, $autoWireHelper);
        }
    }

    /**
     * @param string $serviceName
     * @param AutoWireHelper $autoHelper
     */
    protected function addServiceRecursively(string $serviceName, AutoWireHelper $autoHelper): void
    {

        if (empty($this->services) || !isset($this->services[$serviceName])) {

            $deps = $autoHelper->getServiceParams($serviceName);

            foreach ($deps as $dep) {
                $this->addServiceRecursively($dep, $autoHelper);
            }

            $this->addService($serviceName, $deps);
        }
    }

    /**
     * @param $serviceName
     * @param $params
     */
    public function addService($serviceName, $params = array()): void
    {

        if (empty($this->services) || (!isset($this->services[$serviceName]) && class_exists($serviceName))) {
            $this->services[$serviceName] = $params;
        }
    }

    /**
     * @param $services
     */
    protected function setServicesNormally($services): void
    {
        foreach ($services as $service) {
            $this->addService($service);
        }
    }

    public function isAutoWired(): bool
    {
        return $this->isAutoWired;
    }

    public function setIsAutoWired(bool $isAutoWired, AutoWireHelper $autoWireHelper): void
    {
        $this->isAutoWired = $isAutoWired;
        $this->autoWireHelper = $autoWireHelper;

    }

    /**
     * @param ContainerInterface $container
     */
    public function register(ContainerInterface $container): void
    {
        foreach ($this->services as $serviceName => $params) {
            $container->add($serviceName, $params);
        }
    }
}