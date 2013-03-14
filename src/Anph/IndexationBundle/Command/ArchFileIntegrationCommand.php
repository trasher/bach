<?php
namespace Anph\IndexationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Anph\IndexationBundle\Generator\FileDriverGenerator;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;

class ArchFileIntegrationCommand extends ContainerAwareCommand
{
	protected function configure()
    {
        $this
            ->setName('anph:process:archfileintegration')
            ->setDescription('Integrate archivists file')
        	->setHelp(<<<EOF
The <info>%command.name%</info> command convert a archivist file in format for SOLR engine
EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$integrationService = $this->getContainer()->get('anph.indexation.process.arch_file_integration');
    	
    	$return = $integrationService->integrate();
    	
    	$output->writeln($return);
    }
}