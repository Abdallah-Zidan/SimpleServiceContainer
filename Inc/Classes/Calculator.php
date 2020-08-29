<?php 
namespace Inc\Classes;
class Calculator {
    protected $logger;
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function add(int $num1 , int $num2):int{
            $this->logger->log('adding '.$num1.' to '.$num2);
            return $num1 +$num2;
    }
}