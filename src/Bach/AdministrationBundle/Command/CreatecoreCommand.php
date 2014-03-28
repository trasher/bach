<?php

/**
 * Core creation command
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
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;

/**
 * Core creation command
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class CreatecoreCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:create_core')
            ->setDescription('Solr core creation')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> creates a core with current configuration
EOF
            )->addArgument(
                'name',
                InputArgument::REQUIRED,
                _('Core name')
            )->addArgument(
                'type',
                InputArgument::OPTIONAL,
                _('Core type')
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

        $core_name = $input->getArgument('name');
        $type = $input->getArgument('type');

        if ( !$type ) {
            $type = 'EADFileFormat';
        }

        $configreader = $container->get('bach.administration.configreader');
        $sca = new SolrCoreAdmin($configreader);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $orm_name = 'Bach\IndexationBundle\Entity';
        switch ( $type ) {
        case 'EADFileFormat':
            $orm_name .= '\EADFileFormat';
            break;
        case 'MatriculesFileFormat':
            $orm_name .= '\MatriculesFileFormat';
            break;
        case 'PMBFileFormat':
            $orm_name .= '\PMBFileFormat';
            break;
        default:
            throw new \RuntimeException(
                str_replace(
                    '%type',
                    $type,
                    _('Unkwown type %type')
                )
            );
            break;
        }

        $db_params = $sca->getJDBCDatabaseParameters(
            $this->getContainer()->getParameter('database_driver'),
            $this->getContainer()->getParameter('database_host'),
            $this->getContainer()->getParameter('database_port'),
            $this->getContainer()->getParameter('database_name'),
            $this->getContainer()->getParameter('database_user'),
            $this->getContainer()->getParameter('database_password')
        );

        $result = $sca->create(
            $type,
            $core_name,
            $orm_name,
            $em,
            $db_params
        );

        if ( $result === false ) {
            foreach ( $sca->getErrors() as $e ) {
                $e = str_replace('<br/>', "\n", $e);
                $output->writeln(
                    '<fg=red;options=bold>' . $e . '</fg=red;options=bold>'
                );
            }

            foreach ( $sca->getWarnings() as $w ) {
                $e = str_replace('<br/>', "\n", $w);
                $output->writeln(
                    '<fg=yellow;options=bold>' . $w . '</fg=yellow;options=bold>'
                );
            }
        } else {
            $output->writeln(
                '<fg=green;options=bold>' .
                str_replace(
                    '%corename',
                    $core_name,
                    _('Core %corename has been created and loaded :)')
                ) .
                '</fg=green;options=bold>'
            );
        }
    }
}
