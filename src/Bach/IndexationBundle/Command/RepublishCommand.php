<?php

/**
 * Publish again published files
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
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Publish again published files
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class RepublishCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:republish')
            ->setDescription('Publish again published files')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> publish all existing files.
EOF
            )->addArgument(
                'type',
                InputArgument::OPTIONAL,
                'Documents type. If omitted, all types will be used.'
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

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository('BachIndexationBundle:Document');

        $documents = array();

        foreach ( $types as $type ) {
            $qb = $repo->createQueryBuilder('d')
                ->select('d')
                ->where('d.extension = :ext')
                ->setParameter('ext', $type);

            $query = $qb->getQuery();
            $documents[$type] = $query->getResult();
        }

        $command = $this->getApplication()->find('bach:publish');

        foreach ( $documents as $type=>$docs ) {
            $output->writeln(
                '<info>About to re-publish ' . count($docs) .
                ' documents in ' . $type . '</info>'
            );

            $list = array();
            foreach ( $docs as $doc ) {
                if ( $doc->isUploaded() ) {
                    //FIXME: publication cannot handle uploaded docs
                } else {
                    $list[] = $doc->getPath();
                }
            }

            $arguments = array(
                'command'           => 'bach:publish',
                'type'              => $type,
                '--assume-yes'      => true,
                '--no-change-check' => true,
                'document'          => $list
            );
            $publish_input = new ArrayInput($arguments);
            $code = $command->run($publish_input, $output);
        }
    }
}
