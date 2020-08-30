<?php

use Container\Classes\ProviderFactory;
use Inc\Classes\Logger;
use Inc\Classes\Calculator;
use Container\Classes\SimpleContainer;
use Inc\Classes\Testing;


if (file_exists(__DIR__ . '\\vendor\\autoload.php')
    && file_exists(__DIR__ . '\\config.php')) {

    require_once __DIR__ . '\\vendor\\autoload.php';
    require_once './config.php';

} else {
    echo "consider running composer dump-autoload -o \n also check that config.php exists";
    exit;
}

//************** using auto wiring ***************** //

$provider = ProviderFactory::getProvider();
$provider->addService(Calculator::class,array(Logger::class));
//$provider->setIsAutoWired(true,\Container\Classes\AutoWireHelper::getInstance());
$container = new SimpleContainer();

$container->register($provider);


//short class name or better full class name with namespace using Calculator::class
$calc = $container->get('Calculator');

$res = $calc->add(5,7);
echo $res;
$container->addWithCallback(Testing::class , static function($c,$args){
    return new Testing($c->get('Logger'),...$args);
});
$container->get('Testing',array(10));

echo PHP_EOL;
//****************************************************//

//********************* without auto wiring ***********//

$container = new SimpleContainer();

// =========== register services============//

//using add with callback
$container->addWithCallback(Calculator::class,static function ($container){
    return new Calculator($container->get('Logger'));
});

//using normal add
$container->add(Logger::class);

$container->add(Testing::class,array(Logger::class));

//$container->addWithCallback(\Inc\Classes\Testing::class,static function($c,$args){
//    return new \Inc\Classes\Testing($c->get(Logger::class),...$args);
//});
// =======================//

// use the container
$calc = $container['Calculator'];
echo $calc->add(6,18);
echo PHP_EOL;

//$test = $container->get(Testing::class,array(10));


//print_r($container);




