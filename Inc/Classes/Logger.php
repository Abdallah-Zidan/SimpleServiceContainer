<?php 
namespace Inc\Classes;
class Logger{

    public function log($text): void
    {
        file_put_contents('log.log',$text.PHP_EOL ,FILE_APPEND );
    }
}