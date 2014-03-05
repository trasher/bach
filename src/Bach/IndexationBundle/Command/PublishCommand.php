<?php

/**
 * Publication command
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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
Use Symfony\Component\HttpFoundation\File\File;
use Bach\IndexationBundle\Entity\Document;
use Bach\IndexationBundle\Entity\ArchFileIntegrationTask;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;

/**
 * Publication command
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class PublishCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:publish')
            ->setDescription('File publication')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> launches whole publishing process
(pre-processing, conversion, indexation)
EOF
            )->addArgument(
                'type',
                InputArgument::REQUIRED,
                _('Documents type')
            )->addArgument(
                'document',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                _('Documents names or directories to proceed')
            )->addOption(
                'assume-yes',
                null,
                InputOption::VALUE_NONE,
                _('Assume yes for all questions')
            )->addOption(
                'solr-only',
                null,
                InputOption::VALUE_NONE,
                _('Publish only in solr (full-import)')
            )->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                _('Do not really publish.')
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
        // enable memory profiling
        if (extension_loaded('memprof')) {
            memprof_enable();
        }
        $count = 0;

        $dry = $input->getOption('dry-run');
        if ( $dry === true ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                _('Running in dry mode') .
                '</fg=green;options=bold>'
            );
        }

        $type = null;
        $container = $this->getContainer();
        $known_types = $container->getParameter('bach.types');

        if ( $input->getArgument('type')) {
            $type = $input->getArgument('type');

            if ( !in_array($type, $known_types) ) {
                $msg = _('Unknown type! Please choose one of:');
                throw new \UnexpectedValueException(
                    $msg . "\n -" .
                    implode("\n -", $known_types)
                );
            }
        }

        $output->writeln(
            $msg = str_replace(
                '%type',
                $type,
                _('Publishing "%type" documents.')
            )
        );

        //let's check if files exists
        $to_publish = $input->getArgument('document');

        //let's proceed
        $tf = $container->get('bach.indexation.typesfiles');
        $files_to_publish = $tf->getExistingFiles($type, $to_publish);

        $output->writeln(
            "\n" .  _('Following files are about to be published: ')
        );
        $output->writeln(
            implode("\n", $files_to_publish[$type])
        );

        $confirm = null;
        if ( $input->getOption('assume-yes') ) {
            $confirm = 'yes';
        } else {
            $choices = array(_('yes'), _('no'));
            $dialog = $this->getHelperSet()->get('dialog');
            $confirm = $dialog->ask(
                $output,
                "\n" . _('Are you ok (y/n)?'),
                null,
                $choices
            );
        }

        if ( $confirm === 'yes' || $confirm === 'y' ) {
            $em = $this->getContainer()->get('doctrine')->getManager();

            $output->writeln(
                '<fg=green;options=bold>' .
                _('Publication begins...') .
                '</fg=green;options=bold>'
            );

            if ( $input->getOption('solr-only') ) {
                $steps = 1;
            } else {
                $steps = count($files_to_publish[$type]);
                //add solr step
                $steps++;
            }

            $progress = $this->getHelperSet()->get('progress');
            $progress->start($output, $steps);

            if ( !$input->getOption('solr-only') ) {
                $integrationService = $this->getContainer()
                    ->get('bach.indexation.process.arch_file_integration');

                $docs = array();
                foreach ( $files_to_publish[$type] as $ftp ) {
                    $document = new Document();
                    $document->setFile(new File($ftp));
                    $document->setExtension($type);
                    $document->generateDocId();

                    //check if doc exists
                    $repo = $em->getRepository('BachIndexationBundle:Document');
                    $exists = $repo->findOneByDocid($document->getDocId());
                    if ( $exists ) {
                        $document = $exists;
                        $exists->setFile(new File($ftp));
                        $exists->setUpdated(new \DateTime());
                        $exists->setStoreDir(
                            $this->getContainer()->getParameter('bach.typespaths')[$type]
                        );
                    } else {
                        $document->setCorename(
                            $this->getContainer()->getParameter(
                                $type . '_corename'
                            )
                        );
                        $document->setStoreDir(
                            $this->getContainer()->getParameter('bach.typespaths')[$type]
                        );
                    }

                    if ( $type !== 'matricules' ) {
                        $progress->advance();
                        if ( $dry === false ) {
                            $em->persist($document);
                            $em->flush();
                        }

                        //create a new task
                        $task = new ArchFileIntegrationTask($document);

                        if ( $dry === false ) {
                            $res = $integrationService->integrate($task);
                        }

                        unset($task);
                    } else {
                        $docs[] = $document;
                    }
                    unset($document, $exists);
                }
            }

            if ( $type === 'matricules' && count($docs) > 0 ) {
                $tasks = array();
                $count = 0;
                foreach ( $docs as $document ) {
                    $count++;
                    if ( $dry === false ) {
                        $em->persist($document);
                        if ( $count % 20000 === 0 ) {
                            $em->flush();
                        }
                    }
                    $task = new ArchFileIntegrationTask($document);
                    $tasks[] = $task;
                }
                $em->flush();
                $integrationService->integrateAll($tasks, $progress);
            }

            $this->_solrFullImport($type, $progress, $dry);

            $progress->finish();
        } else {
            $output->writeln(
                '<fg=red;options=bold>' .
                _('Command canceled by user.') .
                '</fg=red;options=bold>'
            );

        }
    }

    /**
     * Proceedd solr full data import
     *
     * @param string  $type     Documents type
     * @param Helper  $progress Progress bar instance
     * @param boolean $dry      Dry run mode
     *
     * @return void
     */
    private function _solrFullImport($type, $progress, $dry)
    {
        $progress->advance();
        $configreader = $this->getContainer()
            ->get('bach.administration.configreader');
        $sca = new SolrCoreAdmin($configreader);
        if ( $dry === false ) {
            $sca->fullImport(
                $this->getContainer()->getParameter($type . '_corename')
            );
        }
    }
}
