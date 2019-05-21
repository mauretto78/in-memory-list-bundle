<?php
/**
 * This file is part of the InMemoryList Bundle package.
 *
 * (c) Mauro Cassani<https://github.com/mauretto78>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InMemoryList\Bundle\Controller;

use InMemoryList\Bundle\Service\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/index", name="inmemorylist_index")
     */
    public function indexAction()
    {
        $items = [];

        /** @var Cache $cache */
        $cache = $this->container->get('in_memory_list');
        foreach ($cache->getClient()->getRepository()->getIndex() as $key => $item) {
            $expire_date = $item['created_on']->add(new \DateInterval('PT'.$item['ttl'].'S'));

            $item['ttl'] = $cache->getClient()->getRepository()->getTtl($item['uuid']);
            $item['created_on'] = $item['created_on']->format("F jS \\a\\t g:ia");
            $item['expires_on'] = $expire_date->format("F jS \\a\\t g:ia");
            $items[$key] = $item;
        }

        return $this->json(
            $items,
            200
        );
    }

    /**
     * @Route("/flush", name="inmemorylist_flush")
     */
    public function flushAction()
    {
        /** @var Cache $cache */
        $cache = $this->container->get('in_memory_list');
        $cache->getClient()->getRepository()->flush();

        return $this->json(
            'Flushed cache',
            204
        );
    }

    /**
     * @Route("/delete/{uuid}", name="inmemorylist_delete_list")
     */
    public function deleteListAction($uuid)
    {
        /** @var Cache $cache */
        $cache = $this->container->get('in_memory_list');
        $cache->getClient()->getRepository()->delete($uuid);

        return $this->json(
            'Deleted list from cache',
            204
        );
    }
}
