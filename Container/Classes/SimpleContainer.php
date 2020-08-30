<?php

namespace Container\Classes;


use ArgumentCountError;
use ArrayObject;
use Container\Interfaces\ContainerInterface;
use Container\Interfaces\ProviderInterface;
use Error;
use Exception;

class SimpleContainer extends ArrayObject implements ContainerInterface
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
        parent::__construct();
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
        foreach (array_keys($arr) as $key) {
            $shortName = $this->extractShortName($key);
            if ($name === $shortName) {
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
     * register any service provider that implements ProviderInterface
     * @param ProviderInterface $provider
     */
    public function register(ProviderInterface $provider): void
    {
        $provider->register($this);
    }

    /**
     * for getting values as arrays for convenience
     * @param mixed $id
     * @return mixed|null
     */
    public function offsetGet($id)
    {
        return $this->get($id);
    }

    /**
     * gets the instance from instances array, if doesn't exist then
     * or create it using its registered callback, if it doesn't exist then
     * create it using its registered service and dependencies, if it doesn't exist then
     * print warning and return null
     * @param string $name
     * @param null $args
     * @return mixed
     */
    public function get(string $name, $args = null)
    {

        $instanceKey = $this->isRegistered($name, $this->_instances);

        if ($instanceKey) {
            return $this->_instances[$instanceKey];
        }

        $callBackKey = $this->isRegistered($name, $this->_callbacks);

        if ($callBackKey) {
            try {
                return $this->_instances[$callBackKey] = call_user_func($this->_callbacks[$callBackKey], $this, $args);

            } catch (ArgumentCountError $error) {
                exit ("\n**** warning*****\nfew arguments were passed to the class $className that could be missing dependencies issue .. check your container\n");

            } catch (Exception | Error $e) {
                exit( $e->getMessage());
            }
        }

        $serviceKey = $this->isRegistered($name, $this->_services);

        if (!$serviceKey) {
            echo("\n****** warning ***********\nthere is a problem with your dependencies recheck them as this service  $name  might not  exist in the container\n");
            exit("if you are using autowired provider check that all services have dependencies as services or add the service manually using addWithCallback method\n\n");
        }

        $this->_instances[$serviceKey] = $this->createObject($this->_services[$serviceKey], $args);

        return $this->_instances[$serviceKey];
    }

    /**
     * create a service if its dependencies are ready
     * if not then create them first
     * @param array $service
     * @param $arguments
     * @return mixed
     */
    protected function createObject(array $service, $arguments)
    {
        $args = array();

        foreach ($service[1] as $param) {
            $args[] = $this->get($param);
        }

        if ($arguments) {
            $args = array_merge($args, $arguments);
        }

        $className = $service[0];

        try {
            return new $className(...$args);

        } catch (ArgumentCountError $error) {
            exit ("\n**** warning*****\nfew arguments were passed to the class $className that could be missing dependencies issue .. check your container\n");

        } catch (Exception | Error $e) {
            exit( $e->getMessage());
        }

    }

}
