<?php

/**
 * Publishing benchmarks
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
                $manager->convert(
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
