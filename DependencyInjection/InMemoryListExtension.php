<?php

namespace InMemoryList\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class InMemoryListExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        /**
         * Includes services_dev.yml only
         * if we are in debug mode
         */
        if(in_array($container->getParameter('kernel.environment'), ['dev', 'test'])){
            $loader->load('services_dev.yml');
        }
    }
}