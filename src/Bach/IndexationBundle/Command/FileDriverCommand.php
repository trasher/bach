<?php
/**
 * Create new file driver from command line
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
use Bach\IndexationBundle\Generator\FileDriverGenerator;

/**
 * Create new file driver from command line
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class FileDriverCommand extends ContainerAwareCommand
{
    protected $generator = null;

    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:generate:filedriver')
            ->setDescription('Create a new file driver for indexation')
            ->addArgument(
                'format',
                InputArgument::REQUIRED,
                'What is the file format process by the driver?'
            )->addArgument(
                'datatype',
                InputArgument::REQUIRED,
                'What is the input data type?'
            )->setHelp(
                <<<EOF
The <info>%command.name%</info> command create a new file driver in order to extend the indexation process
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
        $format = $input->getArgument('format');
        $datatype = $input->getArgument('datatype');

        $generator = $this->getGenerator('parser');
        $namespace = 'Bach\IndexationBundle\Entity\Driver\\'.strtoupper($format);
        $generator->generate($namespace, $format, $datatype);

        $output->writeln('Generating the file driver code: <info>OK</info>');
    }

    /**
     * Get generator
     *
     * @param string $name Generator name
     *
     * @return FileDriverGenerator
     */
    protected function getGenerator($name)
    {
        if (null == $this->generator) {
            $this->generator = new FileDriverGenerator(
                $this->getContainer()->get('filesystem'),
                __DIR__.'/../Resources/skeleton/driver'
            );
        }

        return $this->generator;
    }
}
