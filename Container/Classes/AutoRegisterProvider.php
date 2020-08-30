<?php


namespace Container\Classes;


use Container\Interfaces\ContainerInterface;

class AutoRegisterProvider extends SimpleServiceProvider
{
    protected $autoRegisterPaths;
    protected $autoWireHelper;

    /**
     * @param string[] $autoRegisterPaths
     * @param AutoWireHelper $autoWireHelper
     */
    public function __construct(array $autoRegisterPaths, AutoWireHelper $autoWireHelper)
    {
        parent::__construct();

        $this->autoWireHelper = $autoWireHelper;

        $this->autoRegisterPaths = $autoRegisterPaths;

        $this->isAutoWired=true;
    }

    /**
     * @param ContainerInterface $container
     */
    public function register(ContainerInterface $container): void
    {
        $helper = $this->autoWireHelper;

        $reducer = static function($prev,$path) use ($helper){
            return array_merge($prev,$helper->getServicesFromDirectory($path));
        };

        $services = array_reduce($this->autoRegisterPaths,$reducer , array());

        $this->addServices($services);

        parent::register($container);
    }

}