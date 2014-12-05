<?php

/**
 * OAI repository population command
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

use Symfony\Component\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * OAI repository population command
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class PopulateOaiRepoCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:oai:populate')
            ->setDescription('OAI repository population')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> populates OAI repository
EOF
            )->addOption(
                'max',
                null,
                InputOption::VALUE_REQUIRED,
                _('Maximum results retrived from Solr.'),
                1000000
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
        $doctrine = $container->get('doctrine');

        $logger = $container->get('oai.logger');

        //retrieve published fragments list
        $client = $container->get('solarium.client.ead');
        $query = $client->createSelect();
        $query->setFields(
            array(
                'fragmentid',
                'fragment',
                'parents_titles',
                'archDescUnitTitle'
            )
        );
        //It is not possible to retrieve all rows with Solr...
        $max_rows = $input->getOption('max');
        $query->setRows($max_rows);
        $list = $client->execute($query);

        if ( $list->getNumFound() > $max_rows ) {
            $output->writeln(
                '<fg=red;options=bold>' .
                str_replace(
                    array('%retrieved', '%found'),
                    array($max_rows, $list->getNumFound()),
                    _('%found components found, but only %retrieved has been retrieved!')
                ) . "\n" . _('OAI export has failed :(') .
                '</fg=red;options=bold>'
            );
            return;
        }


        $oai_path = $this->getContainer()->getParameter('ead_oai_path');

        $output->writeln(
            '<fg=green;options=bold>' .
            str_replace(
                '%path',
                $oai_path,
                _('OAI documents will be stored in %path')
            ) .
            '</fg=green;options=bold>'
        );

        $finder = new Finder();
        $finder->followLinks()
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            -> ignoreUnreadableDirs(true)
            ->sortByType();
        $existing = $finder->files()->name('*.xml')->in($oai_path);
        $existing = iterator_to_array($existing);

        foreach ( $list as $fragment ) {
            $path = $oai_path . '/' . $fragment['fragmentid'] . '.xml';
            if ( isset($existing[$path]) ) {
                //remove existing path to get removed fragments list
                unset($existing[$path]);
            }

            //check if contents have changed
            $contents = null;
            $msg = null;
            if ( file_exists($path) ) {
                $contents = file_get_contents($path);
                $msg = str_replace(
                    '%id',
                    $fragment['fragmentid'],
                    _('Change %id')
                );
            } else {
                $msg = str_replace(
                    '%id',
                    $fragment['fragmentid'],
                    _('Create %id')
                );
            }

            $frag = simplexml_load_string($fragment['fragment']);
            $title = (string)$frag->did->unittitle;

            //handle parents titles
            $title .= ' (';
            if ( count($fragment['parents_titles']) > 0 ) {
                $parents_titles = $fragment['parents_titles'];;
                $parents_titles = array_reverse($parents_titles);
                foreach ( $parents_titles as $parent ) {
                    $title .= $parent . ' ; ';
                }
            }
            $title .= $fragment['archDescUnitTitle'];
            $title .= ')';
            $frag->did->unittitle = $title;

            if ( $contents != $frag->asXML() ) {
                $output->writeln('<fg=green>' . $msg . '</fg=green>');
                $logger->info($msg);

                file_put_contents(
                    $path,
                    $frag->asXML()
                );
            }
        }

        foreach ( $existing as $path=>$file ) {
            $msg = str_replace(
                '%id',
                $file->getBaseName('.' . $file->getExtension()),
                _('Remove %id')
            );

            $output->writeln('<fg=green>' . $msg . '</fg=green>');
            $logger->info($msg);

            unlink($path);
        }
    }
}
