<?php
/**
 * This file is part of the InMemoryList Bundle package.
 *
 * (c) Mauro Cassani<https://github.com/mauretto78>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InMemoryList\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class InMemoryListExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load services.yml file
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Includes services_dev.yml only
        // if we are in debug mode
        if (in_array($container->getParameter('kernel.environment'), ['dev', 'test'])) {
            $loader->load('services_dev.yml');
        }

        // set Configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // set Symfony parameter called 'in_memory_list'
        $container->setParameter('in_memory_list', $config);
    }
}
