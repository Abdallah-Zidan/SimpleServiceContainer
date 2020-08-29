<?php


namespace Container;


use BadMethodCallException;
use Container\Interfaces\ProviderInterface;
use Container\Interfaces\ContainerInterface;


abstract class AbstractProvider implements ProviderInterface
{
    protected $services ;

    protected static $_instance;

    protected function __construct()
    {
        $this->services = array();
    }

    public function __clone()
    {
        throw new BadMethodCallException('doing it wrong');
    }

    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @param array $service
     */
    public function addService(array $service): void
    {
        if (isset($service['service']) && class_exists($service['service'])) {
            $this->services[] = $service;
        }
    }

     public function setServices(array $services): void{
        $this->services = array();
         /** @var array $service */
         foreach ($services as $service) {
             $this->addService($service);
         }
     }

    /**
     * @param ContainerInterface $container
     */
    public function register(ContainerInterface $container): void
    {
        foreach ($this->services as $service) {
            $container->add($service['service'], ($service['params'] ?? array()));
        }
    }
}