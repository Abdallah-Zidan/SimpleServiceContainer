<?php


namespace Inc\Classes;


class Testing
{

    protected $logger;

    /**
     * Testing constructor.
     * @param Logger $logger
     * @param int $number
     */
    public function __construct(Logger  $logger,int $number)
    {
        $this->logger = $logger;
        $this->test($number);
    }

    public function test($number): void
    {
        $this->logger->log('from test '.$number.PHP_EOL);
    }
}