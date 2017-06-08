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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('iml:cache:index')
            ->setDescription('Get all data stored in cache.')
            ->setHelp('This command displays in a table all data stored in cache.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            /** @var Client $cache */
            $cache = $this->getContainer()->get('in-memory-list');
            $index = $cache->getIndex(null, true);

            if ($index and count($index)) {
                $table = new Table($output);
                $table->setHeaders(['#', 'List', 'Created on', 'Chunks', 'Chunk size', 'Ttl', 'Items']);

                $counter = 0;
                foreach ($index as $item) {
                    $item = unserialize($item);
                    $listUuid = $item['uuid'];

                    /** @var \DateTimeImmutable $created_on */
                    $created_on = $item['created_on'];
                    $table->setRow(
                        $counter,
                        [
                            $counter+1,
                            '<fg=yellow>'.$listUuid.'</>',
                            $created_on->format('Y-m-d H:i:s'),
                            $cache->getNumberOfChunks($listUuid),
                            $cache->getChunkSize($listUuid),
                            $cache->getTtl($listUuid),
                            $item['size'],
                        ]
                    );
                    ++$counter;
                }

                $table->render();
            } else {
                $output->writeln('<fg=red>[IML] Empty Index.</>');
            }
        } catch (\Exception $e){
            $io->error('[IML] Cache index not available. - ERROR: ' . $e->getMessage() );
        }
    }
}