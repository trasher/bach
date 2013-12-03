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
use Symfony\Component\Console\Output\OutputInterface;

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
    }
}
