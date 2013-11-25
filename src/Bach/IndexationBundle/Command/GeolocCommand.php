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
use Bach\IndexationBundle\Entity\Toponym;

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
                'find',
                null,
                InputOption::VALUE_REQUIRED,
                _('Use specified value for localizations to find (may use % joker)')
            )->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                _('Limit number of results.')
            )->addOption(
                'test',
                null,
                InputOption::VALUE_NONE,
                _('Do not store anything, just query nominatim.')
            )->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                _('Just give a try with parameted occurences, do not store anything.')
            )->addOption(
                'silent',
                null,
                InputOption::VALUE_NONE,
                _('Quiet mode')
            )->addOption(
                'moreverbose',
                null,
                InputOption::VALUE_NONE,
                _('Verbose mode')
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
        $quiet = $input->getOption('silent');
        $verbose = $input->getOption('moreverbose');

        if ( $quiet && $verbose ) {
            $output->writeln(
                '<fg=red;options=bold>' .
                _('You may not use "quiet" and "verbose" together.') .
                '</fg=red;options=bold>'
            );
        }

        $dry = $input->getOption('dry-run');
        if ( $dry === true && !$quiet ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                _('Running in dry mode') .
                '</fg=green;options=bold>'
            );
        }

        $test = $input->getOption('test');
        if ( $test === true && !$quiet ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                _('Running in test mode') .
                '</fg=green;options=bold>'
            );
        }

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $repo = $doctrine->getRepository('BachIndexationBundle:EADIndexes');
        $qb = $repo->createQueryBuilder('a')
            ->select('DISTINCT a.name')
            ->leftJoin(
                'BachIndexationBundle:Geoloc',
                'g',
                'WITH',
                'a.name = g.indexed_name'
            )
            ->where('a.type = :type')
            ->andWhere('g.indexed_name IS NULL')
            ->setParameter('type', 'cGeogname');

        $limit = $input->getOption('limit');
        if ( $limit && !$quiet ) {
            $output->writeln(
                '<fg=green;options=bold>' .
                str_replace(
                    '%i',
                    $limit,
                    _('Set limit to %i entries.')
                ) .
                '</fg=green;options=bold>'
            );
            $qb->setMaxResults($limit);
        }

        $find = $input->getOption('find');
        if ( $find ) {
            if ( !$quiet ) {
                $output->writeln(
                    '<fg=green;options=bold>' .
                    str_replace(
                        '%search',
                        $find,
                        _('Search on: %search')
                    ) .
                    '</fg=green;options=bold>'
                );
            }
            $qb->andWhere('a.name LIKE :name')
                ->setParameter('name', $find);
        }

        $query = $qb->getQuery();

        if ( $verbose ) {
            $output->writeln(
                _('Query:') . "\n" . $query->getSQL()
            );
        }

        $bdd_places = $query->getResult();

        $places = array();
        foreach ( $bdd_places as $p ) {
            //create toponyms & dedup
            if ( !isset($places[$p['name']]) ) {
                try {
                    $places[$p['name']]= new Toponym($p['name']);
                } catch ( \RuntimeException $e ) {
                    //pass
                }
            }
        }

        if ( !$quiet ) {
            $output->writeln(
                str_replace(
                    '%count',
                    count($places),
                    _('Found %count places in the database.')
                ) . "\n\n"
            );
        }

        if ( $test ) {
            $values = array(
                'Aigues-Mortes (Gard, France)',
                'Gévaudan (France ; baillage)',
                'Soyouz (ELS) (Sinnamary, Guyane, France ; ensemble de lancement)'
            );

            $places = array();
            foreach ( $values as $val ) {
                $places[$val] = new Toponym($val);
            }
        }
        $nominatim = $this->getContainer()->get('bach.indexation.Nominatim');

        //stats
        $total = count($places);
        $found = 0;
        $fail = 0;

        foreach ( $places as $orig=>$toponym ) {
            if ( $toponym->canBeLocalized() ) {
                $output->writeln(
                    '<fg=green;>' .
                    str_replace(
                        '%t',
                        $toponym->__toString(),
                        _('Localizing %t...')
                    ) .
                    '</fg=green;>'
                );

                $result = $nominatim->proceed($toponym);

                if ( $result !== false ) {
                    $ent = new Geoloc();
                    $ent->hydrate($toponym, $result);
                    $output->writeln(
                        '<fg=green;>     ' .
                        str_replace(
                            array('%name', '%type'),
                            array($ent->getName(), $ent->getType()),
                            _('Found %name (%type)')
                        ) .
                        '</fg=green;>'
                    );

                    if ( !$dry) {
                        $em->persist($ent);
                    }
                    $found++;
                } else {
                    $fail++;
                }
            } else {
                $fail++;
                $output->writeln(
                    '<fg=red;>' .
                    str_replace(
                        '%t',
                        $toponym->__toString(),
                        _('Toponym %t cannot be localized.')
                    ) .
                    '</fg=red;>'
                );
            }
        }
        if ( !$dry ) {
            $em->flush();
        }

        if ( !$quiet ) {
            $pfail = $fail * 100 / $total;
            $pfound = $found * 100 / $total;

            $color = 'white';
            if ( $found >= $fail ) {
                $color = 'green';
            } else {
                $color = 'red';
            }

            $output->writeln(
                "\n" . '<fg=' .$color . ';options=bold>' .
                str_replace(
                    array(
                        '%found',
                        '%fail',
                        '%total',
                        '%percent'
                    ),
                    array(
                        $pfound,
                        $pfail,
                        $total,
                        ''
                    ),
                    _('Localization finished: %found% found, %fail% fail on %total entries')
                ) .
                '</fg=' .$color . ';options=bold>'
            );
        }
    }
}
