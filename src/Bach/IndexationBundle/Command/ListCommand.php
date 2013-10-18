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
        //$context = $this->getContainer()->get('router')->getContext();
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
        $files  = $tf->getExistingFiles($types);

        $return = '';
        foreach ( $files as $type=>$list ) {
            $this->_parsePaths($list, $type, $return);
        }

        $output->writeln($return);
    }

    /**
     * Parse paths
     *
     * @param array  $entry   Current entry
     * @param string $type    Current type
     * @param string &$return Command output string
     *
     * @return void
     */
    private function _parsePaths($entry, $type, &$return)
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($entry));
        $keys = array();
        foreach ($it as $key => $value) {
            // Build long key name based on parent keys
            if ( is_array($value) ) {
                for ($i = $it->getDepth() - 1; $i >= 0; $i--) {
                    $skey = $it->getSubIterator($i)->key();
                    $key = $skey . '/' . $value;
                }
            } else {
                $key = $value;
            }
            $return .= "\n" . $type . '/' . $key;
        }
    }
}
