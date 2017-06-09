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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('in_memory_list');
        $rootNode
            ->children()
                ->enumNode('driver')
                ->values([
                    'redis',
                    'memcached',
                    'apcu'
                ])
                ->defaultValue('redis')
                ->isRequired()
                ->end()
            ->end()
            ->children()
                ->arrayNode('parameters')
                    ->isRequired()
                    ->prototype('variable')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
