<?php

/**
 * List files that are known
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
/*use Bach\IndexationBundle\Generator\FileDriverGenerator;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;*/

/**
 * List files that are known
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
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
        //$context = $this->getContainer()->get('router')->getContext();
        $container = $this->getContainer();

        $types = $container->getParameter('bach.types');

        if ( $input->getArgument('type')) {
            $type = $input->getArgument('type');
            if ( in_array($type, $types) ) {
                $output->writeln('<info>Working with ' . $type  . ' type.</info>');
                $types[] = $type;
            } else {
                $output->writeln('<error>Unknown type! Please choose one of:' . "\n -" . implode("\n -", $types)  . '</error>');
                die();
            }
        } else {
            $output->writeln('<info>Working with all types (' . implode(', ', $types)   . ')</info>');
        }

        //print_r($types);

        /*$integrationService = $this->getContainer()
            ->get('bach.indexation.process.arch_file_integration');*/
        /*$return = $integrationService->proceedQueue();*/
        /*$output->writeln($return);*/
    }
}
