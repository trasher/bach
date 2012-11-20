<?php
namespace Anph\IndexationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Anph\IndexationBundle\Generator\FileDriverGenerator;

class FileDriverCommand extends ContainerAwareCommand
{
	protected $generator = null;
	
    protected function configure()
    {
        $this
            ->setName('anph:generate:filedriver')
            ->setDescription('Create a new file driver for indexation')
            ->addArgument('format', InputArgument::REQUIRED, 'What is the file format process by the driver?')
        	->addArgument('datatype', InputArgument::REQUIRED, 'What is the input data type?')
        	->setHelp(<<<EOF
The <info>%command.name%</info> command create a new file driver in order to extend the indexation process
EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getArgument('format');
        $datatype = $input->getArgument('datatype');
        
        $generator = $this->getGenerator('parser');
        $namespace = 'Anph\IndexationBundle\Entity\Driver\\'.strtoupper($format);
        $generator->generate($namespace, $format, $datatype);
        
        $output->writeln('Generating the file driver code: <info>OK</info>');
    }
    
    protected function getGenerator($name)
    {
    	if (null == $this->generator) {
    		$this->generator = new FileDriverGenerator($this->getContainer()->get('filesystem'), __DIR__.'/../Resources/skeleton/driver');
    	}
    
    	return $this->generator;
    }
}