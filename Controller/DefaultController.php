<?php

namespace InMemoryList\Bundle\Controller;

use InMemoryList\Bundle\InMemoryListBundle;
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
        foreach ($cache->getClient()->getIndex(null, true) as $key => $item) {
            $data = unserialize($item);

            $expire_date = $data['created_on']->add(new \DateInterval('PT'.$data['ttl'].'S'));

            $data['ttl'] = $cache->getClient()->getTtl($data['uuid']);
            $data['created_on'] = $data['created_on']->format("F jS \\a\\t g:ia");
            $data['expires_on'] = $expire_date->format("F jS \\a\\t g:ia");
            $items[$key] = $data;
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
        $cache->getClient()->flush();

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
        $cache->getClient()->delete($uuid);

        return $this->json(
            'Delete list '.$uuid.' from cache',
            204
        );
    }
}
