<?php
/**
 * This file is part of the InMemoryList Bundle package.
 *
 * (c) Mauro Cassani<https://github.com/mauretto78>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InMemoryList\Bundle\Command;

use InMemoryList\Application\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FlushCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('iml:cache:flush')
            ->setDescription('Flush all data stored in cache.')
            ->setHelp('This command flushes all data stored in cache.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $output->writeln("<fg=yellow>Clearing cache operation can take a while, please be patient...</>");

        try {
            /** @var Client $cache */
            $cache = $this->getContainer()->get('in_memory_list');
            $cache->flush();

            $io->success('[IML] Cache was successful flushed.');
        } catch (\Exception $e) {
            $io->error('[IML] Cache not cleared. - ERROR: ' . $e->getMessage());
        }
    }
}
