<?php
/**
 * This file is part of the InMemoryList Bundle package.
 *
 * (c) Mauro Cassani<https://github.com/mauretto78>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InMemoryList\Bundle\DataCollector;

use InMemoryList\Application\Client;
use InMemoryList\Bundle\InMemoryListBundle;
use InMemoryList\Bundle\Service\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class CacheCollector extends DataCollector
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * CacheCollector constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $size = 0;
        $items = [];

        /** @var Client $client */
        $cacheClient = $this->cache->getClient();
        foreach ($cacheClient->getIndex() as $key => $item) {
            $data = unserialize($item);
            $size = $size + $data['size'];

            $expire_date = $data['created_on']->add(new \DateInterval('PT'.$data['ttl'].'S'));
            $data['expires_on'] = $expire_date;
            $items[$key] = $data;
        }

        $this->data = [
            'bundleVersion' => InMemoryListBundle::VERSION,
            'driverUsed' => $cacheClient->getDriver(),
            'items' => $items,
            'stats' => $cacheClient->getStatistics(),
            'size' => $size,
        ];
    }

    public function getVersion()
    {
        return $this->data['bundleVersion'];
    }

    public function getDriver()
    {
        return $this->data['driverUsed'];
    }

    public function getItems()
    {
        return $this->data['items'];
    }

    public function getStats()
    {
        return $this->data['stats'];
    }

    public function getSize()
    {
        return $this->data['size'];
    }

    public function getName()
    {
        return 'in_memory_list_data_collector';
    }
}
