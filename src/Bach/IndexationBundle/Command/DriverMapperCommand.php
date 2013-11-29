<?php

/**
 * Create new driver mapper from command line
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
use Bach\IndexationBundle\Generator\DriverMapperGenerator;

/**
 * Create new driver mapper from command line
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DriverMapperCommand extends ContainerAwareCommand
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
            ->setName('bach:generate:drivermapper')
            ->setDescription('Create a new driver mapper for indexation')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'What is the name of the mapper?'
            )->setHelp(
                <<<EOF
The <info>%command.name%</info> command create a new driver mapper in order to allow name translation during the import of a file
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
        $name = $input->getArgument('name');

        $generator = $this->getGenerator();
        $namespace = 'Bach\IndexationBundle\Entity\Mapper';
        $mapper = strtoupper($name).'DriverMapper';
        $generator->generate($namespace, $mapper);

        $output->writeln('Generating the mapper driver code: <info>OK</info>');
    }

    /**
     * Get generator
     *
     * @return DriverMapperGenerator
     */
    protected function getGenerator()
    {
        if (null === $this->generator) {
            $this->generator = new DriverMapperGenerator(
                $this->getContainer()->get('filesystem'),
                __DIR__.'/../Resources/skeleton/mapper'
            );
        }

        return $this->generator;
    }
}
