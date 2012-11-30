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

class BenchmarkCommand extends ContainerAwareCommand
{
	protected $generator = null;
	
    protected function configure()
    {
        $this
            ->setName('anph:benchmark')
            ->setDescription('Produce benchmarks')
        	->setHelp(<<<EOF
The <info>%command.name%</info> command produce benchmark of bach project
EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$manager = $this->getContainer()->get('anph_indexation.file_driver_manager');
    	$factory = $this->getContainer()->get('anph_indexation.data_bag_factory'); // Fourni le bon DataBag pour le fichier à indexer
    	$bCount = 1;
    	$type = 'ead';
    	$files = $this->generateSPLs($type);
    	$times = array();
    	$output->writeln('Benchmarks are running...');
    	foreach ($files as $file) {
    		$times[$file->getBasename()] = array();
    		//$output->writeln('Processing for : '.$file->getBasename());
    		
    		for ($i=0;$i<$bCount;$i++) {
    			
	    		$t = microtime(true);
	    		$universalFileFormat = $manager->convert($factory->encapsulate($file),$type);
	    		$time = microtime(true)-$t;
	    		
	    		$times[$file->getBasename()][] = $time;
    		}
    	}
    	$output->writeln("");
    	$output->writeln("Benchmarks");
    	$output->writeln("----------");
    	ksort($times);
    	foreach ($times as $file=>$fTimes) {
    		$mean = array_sum($fTimes)/count($fTimes);
    		$output->writeln($file.' = '.$mean.'s');
    	}
    	$output->writeln("");
    	$output->writeln('Generating benchmarks: <info>OK</info>');
    }
    
    private function generateSPLs($dir) {
    	$spl = array();
    	$finder = new Finder();
    	$finder->files()->in(__DIR__.'/../Resources/benchmark/data/'.strtoupper($dir))->depth('== 0');
    	 
    	foreach ($finder as $file) {
    		$spl[] = $file;
    	}
    	
    	return $spl;
    }
}