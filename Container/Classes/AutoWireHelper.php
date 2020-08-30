<?php


namespace Container\Classes;


use ReflectionClass;
use ReflectionException;

final class AutoWireHelper
{
    protected static $_instance;

    private function __construct()
    {
    }

    public static function getInstance(): AutoWireHelper
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param string $dirPath
     * @return array[]
     */
    public function getServicesFromDirectory(string $dirPath): array
    {

        $files = $this->getDirectoryFiles("$dirPath\\*.php");

        return $this->mapFilesToServicesNames($files);

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
     * @param $files
     * @return array
     */
    protected function mapFilesToServicesNames($files): array
    {
        $names = array_map(static function ($file) {
            return str_replace('.php', '', $file);
        }, $files);

        return array_filter($names, 'class_exists');
    }

    /**
     * get service parameters names using reflection class
     * @param string $serviceName
     * @return array
     */
    public function getServiceParams(string $serviceName): array
    {
        try {
            $class = new ReflectionClass($serviceName);

        } catch (ReflectionException $ex) {
            echo $ex->getMessage();
            exit('error getting class reflection');
        }

        $constructor = $class->getConstructor();

        if ($constructor) {
            $parameters = $constructor->getParameters();
            return array_filter( array_map(static function ($p) {
                if($p->getClass()){
                    return $p->getClass()->getName();
                }
                return  -1;
            }, $parameters),'class_exists');
        }

        return array();
    }
}