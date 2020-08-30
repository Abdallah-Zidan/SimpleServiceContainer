<?php

define('AUTO_WIRE', true);

define('AUTO_REGISTER',false);

define('AUTO_REGISTER_PATHS',
    array(
        'Inc\\Classes'
    )
);

define('SERVICES',array(
    \Inc\Classes\Calculator::class,
    \Inc\Classes\Testing::class
));

