<?php


namespace Container\Interfaces;

interface ProviderInterface
{
    public function register(ContainerInterface $container);
}