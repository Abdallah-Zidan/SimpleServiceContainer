<?php

use Inc\Classes\Logger;
use Inc\Classes\Calculator;
use Container\SimpleContainer;
use Container\AutowiredProvider;

if (file_exists(__DIR__ . '\\vendor\\autoload.php')
    && file_exists(__DIR__ . '\\config.php')) {

    require_once __DIR__ . '\\vendor\\autoload.php';
    require_once './config.php';

} else {
    echo "consider running composer dump-autoload -o \n also check that config.php exists";
    exit;
}

//************** using auto wiring ***************** //

$auto = AutowiredProvider::getInstance(AUTO_WIRE_PATH);

$container = new SimpleContainer();

$container->register($auto);

//short class name or better full class name with namespace using Calculator::class
$calc = $container->get('Calculator');

$res = $calc->add(5,7);
echo $res;
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

// =======================//

// use the container
$calc = $container['Calculator'];
echo $calc->add(6,18);
echo PHP_EOL;


print_r($container);




