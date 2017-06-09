<?php
/**
 * This file is part of the InMemoryList Bundle package.
 *
 * (c) Mauro Cassani<https://github.com/mauretto78>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InMemoryList\Bundle\Twig;

class UnserializeExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('item', [$this, 'unserializeFunction']),
        );
    }

    public function unserializeFunction($string)
    {
        return unserialize($string);
    }
}
