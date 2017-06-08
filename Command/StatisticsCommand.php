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

class StatisticsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('iml:cache:statistics')
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
            $statistics = $cache->getStatistics();

            $table = new Table($output);
            $table->setHeaders(['Key', 'Value']);

            $counter = 0;
            foreach ($statistics as $infoKey => $infoData) {
                $dataString = '';

                if (is_array($infoData)) {
                    foreach ($infoData as $key => $value) {
                        $valueToDisplay = (is_array($value)) ? implode(',', $value) : $value;
                        $dataString .= '['.$key.']->' . $valueToDisplay . "\xA";
                    }
                } else {
                    $dataString .= $infoData;
                }

                $table->setRow(
                    $counter,
                    [
                        $infoKey,
                        $dataString
                    ]
                );
                ++$counter;
            }

            $table->render();

        } catch (\Exception $e){
            $io->error('[IML] Cache statistics not available. - ERROR: ' . $e->getMessage() );
        }
    }
}
