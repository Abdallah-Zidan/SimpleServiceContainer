<?php


namespace Container\Interfaces;

interface ContainerInterface
{
    public  function register(ProviderInterface $provider);
}