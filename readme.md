
## Simple service container and provider  
##### This is just a simple approach to manage services and their providers  
##### you can add services to the container directly using 2 methods .

1. pass an array having the service full name with namespace and array of its dependencies from the other services.  

2. pass a callback which defines how the instance should be created, and it's parameters, and you get an instance of the container passed in the callback.  

```php 
<?php 
$container = new SimpleContainer();

// first way to add 
$container->add(Logger::class);

// adding services using a callback
$container->addWithCallback(Calculator::class,static function ($container){  
    return new Calculator($container->get('Logger'));  
});
```

#### some notes:
- order of services in the container doesn't matter as objects aren't created directly after adding services. objects only get created when 
 required.
 - what's important is that every service depends on services, these services must exist in the container before asking for an instance of that service.
 - dependencies can be passed as an array using the first adding method or passed in the service constructor using callback method as shown in second method .
 
 #### other features
 1. you can pass a key while adding the service and that will be the handle to get the service from the container

```php
// now you can access the service with the key logger
$container->add(Logger::class,array(),'logger');
```

2. you can access the service using it's full name or short name (name of the class without namespace) but be careful that might not work fine if you have 2 classes with the exact short name.
```php 
// using short name 
$calc = $container['Calculator'];  
// using full name  
$calc = $container[Calculator::class];  
  
// using get method  
$calc = $container->get('Calculator');  
$calc = $container->get(Calculator::class);
```
### service providers
- you can define service providers and register them easily into the container
if you implement ProviderInterface

- there are 2 providers implemented already one is a normal provider
that just do nothing but register some services into the container.
the second one is auto wired that can have autowired path and auto register all
the services in that path with the help of Reflection class and in that case 
all services must define the type of its dependencies.

```php

//************** using auto wiring ***************** //

$auto = new AutowiredProvider(AUTO_WIRE_PATH);

$container = new SimpleContainer();

$container->register($auto);

$calc = $container->get('Calculator');


//************** using normal provider ***************** //

$services = array(
    array(
        'service'=>Calculator::class,
        'params'=>array(
            Logger::class,
         )
    ),
    array(
        'service'=>Logger::class
    ),
);

$normal = new NormalProvider($services);
$container->register($normal);

$calc = $container[Calculator::class];

```