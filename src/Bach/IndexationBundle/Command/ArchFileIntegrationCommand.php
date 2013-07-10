<?php
/**
 * Archival file integration in database from command line
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
use Bach\IndexationBundle\Generator\FileDriverGenerator;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Archival file integration in database from command line
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class ArchFileIntegrationCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:process:archfileintegration')
            ->setDescription('Integrate archivists file')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command convert a archivist file in format for SOLR engine
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
        $integrationService = $this->getContainer()
            ->get('bach.indexation.process.arch_file_integration');
        $return = $integrationService->proceedQueue();
        $output->writeln($return);
    }
}
