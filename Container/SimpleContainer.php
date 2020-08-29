<?php

namespace Container;

use Container\Interfaces\ProviderInterface;
use Container\Interfaces\ContainerInterface;

class SimpleContainer implements ContainerInterface
{

    /**
     * the array of created instances that exist as objects in memory already
     * @var array
     */
    protected $_instances;

    /**
     * the array of services that were added to the container whether there
     * are instances of them created in memory or not
     * @var array
     */
    protected $_services;

    /**
     * array of callbacks that defines how an instance should be created
     * when needed .. addWithCallback function is used to add to this array
     * @var
     */
    protected $_callbacks;

    public function __construct()
    {
        $this->_instances = array();
        $this->_services = array();
        $this->_callbacks = array();
    }

    public function getInstances(): array
    {
        return $this->_instances;
    }

    public function getServices(): array
    {
        return array_merge(array_keys($this->_services), array_keys($this->_callbacks));
    }

    /**
     * adds new service to registered services
     * @param string $serviceName
     * @param array $params
     * @param bool | string $key
     */
    public function add(string $serviceName, array $params = array(), $key = false): void
    {
        if (class_exists($serviceName)) {

            $key = $key ?: $serviceName;
            if (!$this->isRegistered($key, $this->_services)) {

                foreach ($params as $param) {
                    if (!class_exists($param)) {
                        return;
                    }
                }

                $this->_services[$key] = array($serviceName, $params);
            }
        } else {
            echo 'no class with the name : ' . $serviceName . PHP_EOL;
        }
    }

    /**
     * checks if service name exists in the provided array
     * @param string $name
     * @param array $arr
     * @return false|string
     */
    protected function isRegistered(string $name, array $arr)
    {
        if (isset($arr[$name])) {
            return $name;
        }

        // maybe the requested name is already short and registered full
        foreach (array_keys($arr) as $key){
            $shortName = $this->extractShortName($key);
            if($name === $shortName){
                return $key;
            }
        }

        return false;
    }

    /**
     * this function returns only name of the service and removes it's namespace
     * for shorter arrays keys and to allow getting instances with short names
     * if needed
     * @param string $serviceName
     * @return string
     */
    protected function extractShortName(string $serviceName): string
    {
        $arr = explode('\\', $serviceName);
        return $arr[count($arr) - 1];
    }

    /**
     * for services which requires special creation this allows defining
     * how class should be created
     * @param string $serviceName
     * @param callable $callback
     * @param bool | string $key
     */
    public function addWithCallback(string $serviceName, callable $callback, $key = false): void
    {
        $key = $key ?: $serviceName;
        $this->_callbacks [$key] = $callback;
    }

    /**
     * gets the instance from instances array, if doesn't exist then
     * or create it using its registered callback, if it doesn't exist then
     * create it using its registered service and dependencies, if it doesn't exist then
     * print warning and return null
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {

        $instanceKey = $this->isRegistered($name, $this->_instances);

        if ($instanceKey) {
            return $this->_instances[$instanceKey];
        }

        $callBackKey = $this->isRegistered($name, $this->_callbacks);

        if ($callBackKey) {
            return $this->_instances[$callBackKey] = call_user_func($this->_callbacks[$callBackKey], $this);
        }

        $serviceKey = $this->isRegistered($name, $this->_services);
        if (!$serviceKey) {
            echo("\n****** warning ***********\nthere is a problem with your dependencies recheck them as this service  $name  might not  exist in the container\n" );
            echo("if you are using autowired provider check that all services have dependencies as services or add the service manually using addWithCallback method\n\n");
            return null;
        }

        $this->_instances[$serviceKey] = $this->createObject($this->_services[$serviceKey]);

        return $this->_instances[$serviceKey];
    }

    /**
     * create a service if its dependencies are ready
     * if not then create them first
     * @param array $service
     * @return mixed
     */
    protected function createObject(array $service)
    {
        $args = array();

        foreach ($service[1] as $param) {
            $args[] = $this->get($param);
        }

        $className = $service[0];

        return new $className(...$args);

    }

    /**
     * register any service provider that implements ProviderInterface
     * @param ProviderInterface $provider
     */
    public function register(ProviderInterface $provider): void
    {
        $provider->register($this);
    }
}
