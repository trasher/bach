<?php

/**
 * List files that are known
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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * List files that are known
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class ListCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:publish:list')
            ->setDescription('List files that would be published')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> gives you the list of files
that would be published.
EOF
            )->addArgument(
                'type',
                InputArgument::OPTIONAL,
                'Documents type to list. If omitted, all types will be listed.'
            )->addOption(
                'stats',
                null,
                InputOption::VALUE_NONE,
                _('Give stats informations (memory used, etc)')
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
        $stats = $input->getOption('stats');
        if ( $stats === true ) {
            $start_time = new \DateTime();
        }

        $container = $this->getContainer();

        $known_types = $container->getParameter('bach.types');
        $types = array();

        if ( $input->getArgument('type')) {
            $type = $input->getArgument('type');
            if ( in_array($type, $known_types) ) {
                $output->writeln('<info>Working with ' . $type  . ' type.</info>');
                $types[] = $type;
            } else {
                $msg = _('Unknown type! Please choose one of:');
                throw new \UnexpectedValueException(
                    $msg . "\n -" .
                    implode("\n -", $known_types)
                );
            }
        } else {
            $types = $known_types;
            $output->writeln(
                '<info>Working with all types (' .
                implode(', ', $types)   . ')</info>'
            );
        }

        $tf = $container->get('bach.indexation.typesfiles');

        foreach ( $types as $type ) {
            $files  = $tf->getExistingFiles($type);
            $output->writeln(
                $type . '/' . implode("\n" .$type . '/', $files[$type])
            );
        }

        if ( $stats === true ) {
            $peak = $this->formatBytes(memory_get_peak_usage());

            $end_time = new \DateTime();
            $diff = $start_time->diff($end_time);

            $hours = $diff->h;
            $hours += $diff->days * 24;

            $elapsed = str_replace(
                array(
                    '%hours',
                    '%minutes',
                    '%seconds'
                ),
                array(
                    $hours,
                    $diff->i,
                    $diff->s
                ),
                '%hours:%minutes:%seconds'
            );

            $output->writeln('Time elapsed: ' . $elapsed);
            $output->writeln('Memory peak: ' . $peak);
        }
    }

    /**
     * Format bytes to human readable value
     *
     * @param int $bytes Bytes
     *
     * @return string
     */
    public function formatBytes($bytes)
    {
        $multiplicator = 1;
        if ( $bytes < 0 ) {
            $multiplicator = -1;
            $bytes = $bytes * $multiplicator;
        }
        $unit = array('b','kb','mb','gb','tb','pb');
        $fmt = @round($bytes/pow(1024, ($i=floor(log($bytes, 1024)))), 2)
            * $multiplicator . ' ' . $unit[$i];
        return $fmt;
    }
}
