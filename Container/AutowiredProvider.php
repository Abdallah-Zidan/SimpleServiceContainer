<?php


namespace Container;

use ReflectionClass;
use ReflectionException;

class AutowiredProvider extends AbstractProvider
{

    /**
     * @param string  $autowiredPath
     * @return AutowiredProvider
     */
    public static function getInstance(string $autowiredPath): AbstractProvider
    {
        if (!$autowiredPath) {
            exit("\n******** warning ********\nyou must provide an array of services using normal provider or auto wiring path\n\n");
        }

        if (!self::$_instance) {

            self::$_instance = new self();

            try {
                $services = self::$_instance->getAutowiredServices($autowiredPath);
            } catch (ReflectionException $e) {
                echo $e->getMessage();
                exit("\n******** warning ********\ncould not auto wire classes ... you should consider trying normal provider\n\n");
            }

            self::$_instance->setServices($services);
        }

        return self::$_instance;
    }

    /**
     * @param string $autowiredPath
     * @return array[]
     * @throws ReflectionException
     */
    protected function getAutowiredServices(string $autowiredPath): array
    {

        $files = $this->getDirectoryFiles("$autowiredPath\\*.php");

        $servicesNames = array_map(static function ($file) {
            return str_replace('.php', '', $file);
        }, $files);

        $servicesNames = array_filter($servicesNames, 'class_exists');

        return array_map(function ($serviceName) {
            return array(
                'service' => $serviceName,
                'params' => $this->getServiceParams($serviceName)
            );
        }, $servicesNames);

    }

    /**
     * gets files from a directory in a recursive way
     * @param $pattern
     * @param int $flags
     * @return array
     */
    protected function getDirectoryFiles($pattern, $flags = 0): array
    {
        $files = glob($pattern, $flags);

        $dirs = glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);

        foreach ($dirs as $dir) {
            $files[] = $this->getDirectoryFiles($dir . '/' . basename($pattern), $flags);
        }
        return $files;
    }

    /**
     * get service parameters names using reflection class
     * @param string $serviceName
     * @return array
     * @throws ReflectionException
     */
    protected function getServiceParams(string $serviceName): array
    {
        $class = new ReflectionClass($serviceName);
        $constructor = $class->getConstructor();

        if ($constructor) {
            $parameters = $constructor->getParameters();
            return array_map(static function ($p) {
                return $p->gettype();
            }, $parameters);
        }

        return array();
    }

}