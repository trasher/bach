<?php

/**
 * Publishing benchmarks
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;


/**
 * Publishing benchmarks
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class BenchmarkCommand extends ContainerAwareCommand
{

    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:benchmark')
            ->setDescription('Produce benchmarks')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command produce benchmark of bach project
EOF
            );
    }

    /**
     * Executes the command
     *
     * @param InputInterface  $input  Stdin
     * @param OutputInterface $output Stdout
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('bach.indexation.file_driver_manager');
        //Databag provider
        $factory = $this->getContainer()->get('bach.indexation.data_bag_factory');
        $bCount = 1;
        $type = 'ead';
        $files = $this->_getBenchFiles($type);
        $times = array();
        $output->writeln('Benchmarks are running...');
        foreach ($files as $file) {
            $times[$file->getBasename()] = array();
            $output->writeln('Processing file: '.$file->getBasename());

            for ($i=0;$i<$bCount;$i++) {

                $t = microtime(true);
                $universalFileFormat = $manager->convert(
                    $factory->encapsulate($file),
                    $type
                );
                $time = microtime(true)-$t;

                $times[$file->getBasename()][] = $time;
            }
        }
        $output->writeln("");
        $output->writeln("Benchmarks");
        $output->writeln("----------");
        ksort($times);
        foreach ($times as $file=>$fTimes) {
            $mean = array_sum($fTimes)/count($fTimes);
            $output->writeln($file.' = '.$mean.'s');
        }
        $output->writeln("");
        $output->writeln('Generating benchmarks: <info>OK</info>');
    }

    /**
     * Retrieve benchmark files
     *
     * @param string $dir Files subdirectory fr type
     *
     * @return array
     */
    private function _getBenchFiles($dir)
    {
        $spl = array();
        $finder = new Finder();
        $finder->files()->in(
            __DIR__ . '/../Resources/benchmark/data/' . strtoupper($dir)
        )->depth('== 0');

        foreach ($finder as $file) {
            $spl[] = $file;
        }

        return $spl;
    }
}
