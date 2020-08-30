<?php


namespace Container\Classes;


final class ProviderFactory
{
    private function __construct(){

    }

    public static function getProvider(): SimpleServiceProvider
    {
        $autoHelper = AutoWireHelper::getInstance();

        if(AUTO_REGISTER){
            return new AutoRegisterProvider(AUTO_REGISTER_PATHS,$autoHelper);
        }

        if(!SERVICES){
            exit ("consider adding services to your config file or use auto registering mode".PHP_EOL);
        }

        if(AUTO_WIRE){
            return  new NormalProvider(SERVICES,true,$autoHelper);
        }

        return new NormalProvider(SERVICES);
    }
}