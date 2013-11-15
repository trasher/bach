<?php
/**
 * Geolocalization command
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
use Bach\IndexationBundle\Entity\Geoloc;
use Bach\IndexationBundle\Entity\EADIndexes;

/**
 * Geolocalization command
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class GeolocCommand extends ContainerAwareCommand
{
    /**
     * Configures command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('bach:geoloc')
            ->setDescription('Geolocalisation')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> retrieve Geolocalization
informations from OSM Nominatim, and store them in the
database.
EOF
            )->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                _('Just give a try at the first occurence, do not store anything.')
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
        $count = 0;

        $dry = $input->getOption('dry-run');
        if ( $dry === true ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                _('Running in dry mode') .
                '</fg=green;options=bold>'
            );
        }

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $repo = $doctrine->getRepository('BachIndexationBundle:EADIndexes');
        $query = $repo->createQueryBuilder('a')
            ->select('DISTINCT a.name')
            ->leftJoin(
                'BachIndexationBundle:Geoloc',
                'g',
                'WITH',
                'a.name = g.indexed_name'
            )
            ->where('a.type = :type')
            ->andWhere('g.indexed_name IS NULL')
            ->setParameter('type', 'cGeogname')
            ->getQuery();
        $bdd_places = $query->getResult();

        $places = array();
        foreach ( $bdd_places as $p ) {
            //clean & dedup
            list($newname,) = explode('(', $p['name']);
            if ( !isset($places[$p['name']]) ) {
                $places[$p['name']]= $newname;
            }
        }

        echo 'Search for ' . count($places) . " places.\n\n";

        //For test purposes
        //$places = array('Aigues-Mortes (Gard, France)' => 'Aigues-Mortes');
        $nominatim = $this->getContainer()->get('bach.indexation.Nominatim');

        foreach ( $places as $orig=>$place ) {
            $result = $nominatim->retrieveCity($place);

            if ( $result !== false ) {
                $ent = new Geoloc($orig, $place, $result);
                $em->persist($ent);
            }
        }
        $em->flush();
    }
}
