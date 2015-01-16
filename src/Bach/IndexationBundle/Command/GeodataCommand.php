<?php
/**
 * Geolocalization command
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
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\File\File;
use Bach\IndexationBundle\Entity\Document;
use Bach\IndexationBundle\Entity\Geoloc;

/**
 * Geolocalization command
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class GeodataCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:geodata')
            ->setDescription('Add Geographic localizations Data ')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> Add Geolocalization
informations from BDD.
EOF
            )->addArgument(
                'database',
                InputArgument::REQUIRED,
                _('Documents type')
            )->addArgument(
                'document',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                _('Documents names or directories to proceed')
            )->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                _('Limit number of results.')
            )->addOption(
                'test',
                null,
                InputOption::VALUE_NONE,
                _('Do not store anything, just query nominatim.')
            )->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                _('Just give a try with parameted occurences, do not store anything.')
            )->addOption(
                'silent',
                null,
                InputOption::VALUE_NONE,
                _('Quiet mode')
            )->addOption(
                'moreverbose',
                null,
                InputOption::VALUE_NONE,
                _('Verbose mode')
            )->addOption(
                'with-notfound',
                null,
                InputOption::VALUE_NONE,
                _('Include results marked as not found')
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
        $quiet = $input->getOption('silent');
        $verbose = $input->getOption('moreverbose');

        if ( $quiet && $verbose ) {
            $output->writeln(
                '<fg=red;options=bold>' .
                _('You may not use "quiet" and "verbose" together.') .
                '</fg=red;options=bold>'
            );
        }

        $dry = $input->getOption('dry-run');
        if ( $dry === true && !$quiet ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                _('Running in dry mode') .
                '</fg=green;options=bold>'
            );
        }

        $test = $input->getOption('test');
        if ( $test === true && !$quiet ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                _('Running in test mode') .
                '</fg=green;options=bold>'
            );
        }

        $container = $this->getContainer();
        $known_types = $container->getParameter('bach.types');

        if ( $input->getArgument('database')) {
            $database = $input->getArgument('database');

            if ( !in_array($database, $known_types) ) {
                $msg = _('Unknown type! Please choose one of:');
                throw new \UnexpectedValueException(
                    $msg . "\n -" .
                    implode("\n -", $known_types)
                );
            }
        }

        $output->writeln(
            $msg = str_replace(
                '%database',
                $database,
                _('Process "%database" documents.')
            )
        );

        //let's check if files exists
        $to_process = $input->getArgument('document');

        //let's proceed
        $tf = $container->get('bach.indexation.typesfiles');
        $files_to_process = $tf->getExistingFiles($database, $to_process);

        $output->writeln(
            "\n" .  _('Following files are about to be process: ')
        );
        $output->writeln(
            "\n" . implode("\n", $files_to_process[$database])
        );

        $database = $input->getArgument('database');
        $know_databases = array(
            'ead'               => _('EAD indexes'),
            'matricules'   => _('Matricules'),
        );
        if ( $database && !isset($know_databases[$database]) ) {
            $know_str = '';
            foreach ( $know_databases as $name=>$info ) {
                $know_str .= "\n\t- " . $name . ' (' . _('for') . ' ' . $info . ')';
            }
            $output->writeln(
                "\n" . '<fg=red;options=bold>' . _('Unknown database!') .
                '</fg=red;options=bold>' .
                "\n" . '<fg=red>' . _('Please enter either:') . $know_str .
                '</fg=red>'
            );
            throw new \RuntimeException(_('Unknown database!'));
        }

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $limit = $input->getOption('limit');
        if ( $limit && !$quiet ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                str_replace(
                    '%i',
                    $limit,
                    _('Set limit to %i entries.')
                ) .
                '</fg=green;options=bold>'
            );
        }

        $with_notfound = $input->getOption('with-notfound');
        if ( $with_notfound && !$quiet ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                _('Include results marked as not found') .
                '</fg=green;options=bold>'
            );
        }

        if ( !$database || $database === 'ead' ) {
            $query = $em->createQuery(
                'SELECT a.indexed_name, a.lat, a.lon AS
                FROM BachIndexationBundle:Geoloc a
                index by a.indexed_name where a.found = 1'
            );

            if ( $limit ) {
                $query->setMaxResults($limit);
            }

            if ( $verbose ) {
                $output->writeln(
                    _('Query ead:') . "\n" . $query->getSQL()
                );
            }

            $geodata = $query->getResult();
        }
        if ( !empty($files_to_process) ) {
            $filename = 'app/cache/console/export';
            if (file_exists($filename) && is_dir($filename)) {
                echo "Le dossier $filename existe.";
            } else {
                echo "Le dossier $filename n'existe pas.";
                mkdir($filename);
            }
        }
        foreach ( $files_to_process[$database] as $filesvalues ) {
            if ( file_exists($filesvalues) ) {
                $xml = simplexml_load_file($filesvalues);
                $results = $xml->xpath('.//geogname');
                foreach ( $results as $result ) {
                    $resultNameIndex = (string)$result;
                    if ( array_key_exists($resultNameIndex, $geodata) ) {
                        $result->addAttribute(
                            'longitude',
                            $geodata[$resultNameIndex]['lon']
                        );
                        $result->addAttribute(
                            'latitude',
                            $geodata[$resultNameIndex]['lat']
                        );
                    }
                }
                $file = 'app/cache/console/export/CONV_'.basename($filesvalues);
                file_put_contents($file, $xml->asXML());
            } else {
                exit('Echec lors de l\'ouverture du fichier '.$filesvalues);
            }
        }
    }
}
