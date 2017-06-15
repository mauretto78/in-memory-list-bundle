<?php
/**
 * This file is part of the InMemoryList Bundle package.
 *
 * (c) Mauro Cassani<https://github.com/mauretto78>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InMemoryList\Bundle\Service;

use InMemoryList\Application\Client;

class Cache
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Cache constructor.
     *
     * @param array $config
     *
     */
    public function __construct(array $config = [])
    {
        $this->_setClient($config);
    }

    /**
     * @param $config
     */
    private function _setClient($config)
    {
        $this->client = new Client($config['driver'], $config['parameters']);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
