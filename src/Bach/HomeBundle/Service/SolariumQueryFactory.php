<?php
/**
 * Bach Solarium query factory
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Service;

use Symfony\Component\Finder\Finder;
use Bach\HomeBundle\Entity\ViewParams;
use Bach\HomeBundle\Entity\SolariumQueryContainer;
use Bach\HomeBundle\Entity\SolariumQueryDecoratorAbstract;
use Solarium\QueryType\Select\Result\Facet\Field;
use Doctrine\ORM\EntityRepository;

/**
 * Bach Solarium query factory
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SolariumQueryFactory
{
    private $_client;
    private $_decorators = array();
    private $_request;
    private $_highlitght;
    private $_spellcheck;
    private $_query;
    private $_qry_facets_fields = array(
        'dao',
        'cDate',
        'cGeogname'
    );

    private $_max_low_date;
    private $_max_up_date;

    private $_low_date;
    private $_up_date;
    private $_date_gap;

    /**
     * Factory constructor
     *
     * @param \Solarium\Client $client Solarium client
     */
    public function __construct(\Solarium\Client $client)
    {
        $this->_client = $client;
        $this->_searchQueryDecorators();
    }

    /**
     * Prepare solr Query
     *
     * @param SolariumQueryContainer $container Solarium container
     *
     * @return void
     */
    public function prepareQuery(SolariumQueryContainer $container)
    {
        $this->_buildQuery($container);
    }

    /**
     * Perform a query into Solr
     *
     * @param SolariumQueryContainer $container Solarium container
     * @param array                  $facets    Facets
     * @param boolean                $show_map  Is map displayed?
     *
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function performQuery(SolariumQueryContainer $container, $facets, $show_map)
    {
        //create query
        if ( !$this->_query ) {
            $this->_buildQuery($container);
        }

        //dynamically create facets
        $this->_addFacets($facets, $show_map);

        $this->_request = $this->_client->createRequest($this->_query);
        $rs = $this->_client->select($this->_query);
        $this->_highlitght = $rs->getHighlighting();
        $this->_spellcheck = $rs->getSpellcheck();
        return $rs;
    }

    /**
     * Build query
     *
     * @param SolariumQueryContainer $container Solarium container
     *
     * @return void
     */
    private function _buildQuery($container)
    {
        $this->_query = $this->_client->createSelect();

        $hl = $this->_query->getHighlighting();
        $hl_fields = '';
        $spellcheck = $this->_query->getSpellcheck();

        foreach ( $container->getFilters() as $name=>$value ) {
            $i = 0;
            foreach ( $value as $v ) {
                if ( $name === 'cDateBegin' ) {
                    //no $i in name here since we only want ONE begin
                    //and end date filter
                    $this->_query->createFilterQuery($name)
                        ->setQuery('+' . $name . ':[' . $v . 'T00:00:00Z TO *]');
                } else if ( $name === 'cDateEnd' ) {
                    //no $i in name here since we only want ONE begin
                    //and end date filter
                    $this->_query->createFilterQuery($name)
                        ->setQuery('+' . $name . ':[* TO ' . $v . 'T00:00:00Z]');
                } else if ( $name === 'dao' ) {
                    $query = null;
                    if ( $v === _('Yes') ) {
                        $query = '+' . $name . ':*';
                    } else {
                        $query = '-' . $name . ':*';
                    }
                    $this->_query->createFilterQuery($name)
                        ->setQuery($query);
                } else if ( $name === 'cDate' ) {
                    if ( strpos('|', $v === false) ) {
                        throw new \RuntimeException('Invalid date range!');
                    } else {
                        list($start, $end) = explode('|', $v);
                        $bdate = new \DateTime($start);
                        $edate = new \DateTime($end);
                        $this->_query->createFilterQuery($name)
                            ->setQuery(
                                '+cDateBegin:[' .
                                $bdate->format('Y-m-d\TH:i:s\Z') .
                                ' TO ' .
                                $edate->format('Y-m-d\TH:i:s\Z')  . ']'
                            );
                    }
                } else {
                    $this->_query->createFilterQuery($name . $i)
                        ->setQuery('+' . $name . ':"' . $v . '"');
                }
                $i++;
            }
        }

        if ( $container->isOrdered() ) {
            $qry = $this->_query;
            $order = $container->getOrderField();
            $direction = $qry::SORT_DESC;

            if ($container->getOrderDirection() === ViewParams::ORDER_ASC) {
                $direction = $qry::SORT_ASC;
            }

            if ( is_array($order) ) {
                foreach ( $order as $field) {
                    $this->_query->addSort(
                        $field,
                        $direction
                    );
                }
            } else {
                $this->_query->addSort(
                    $container->getOrderField(),
                    $direction
                );
            }
        }

        foreach ( $container->getFields() as $name=>$value ) {
            if ( array_key_exists($name, $this->_decorators) ) {
                //Decorate the query
                $this->_decorators[$name]->decorate($this->_query, $value);
                if ( method_exists($this->_decorators[$name], 'getHlFields') ) {
                    if ( trim($hl_fields) !== '' ) {
                        $hl_fields .=',';
                    }
                    $hl_fields .= $this->_decorators[$name]->getHlFields();
                }
            }
        }

        $hl->setFields($hl_fields);
        //on highlithed unititles, we always want the full string
        $hl->getField('cUnittitle')->setFragSize(0);
    }

    /**
     * Dynamically add facets to query
     *
     * @param array   $facets   Facets
     * @param boolean $show_map Is map displayed?
     *
     * @return void
     */
    private function _addFacets($facets, $show_map)
    {
        $facetSet = $this->_query->getFacetSet();
        $facetSet->setLimit(-1);
        $facetSet->setMinCount(1);
        $map_facet = false;

        foreach ( $facets as $facet ) {
            if ( !in_array($facet->getSolrFieldName(), $this->_qry_facets_fields) ) {
                $facetSet->createFacetField($facet->getSolrFieldName())
                    ->setField($facet->getSolrFieldName());
            } else {
                switch($facet->getSolrFieldName()) {
                case 'dao':
                    $fmq = $facetSet->createFacetMultiQuery('dao');
                    $fmq->createQuery(_('Yes'), 'dao:*');
                    $fmq->createQuery(_('No'), '-dao:*');
                    break;
                case 'cDate':
                    if ( isset($this->_low_date) && isset($this->_up_date) ) {
                        $fr = $facetSet->createFacetRange('cDate');
                        $fr->setField('cDateBegin');
                        $fr->setStart($this->_low_date);
                        $fr->setgap('+' . $this->_date_gap . 'YEARS');
                        $fr->setEnd($this->_up_date);
                    }
                    break;
                case 'cGeogname':
                    $facetSet->createFacetField($facet->getSolrFieldName())
                        ->setField($facet->getSolrFieldName());
                    if ( $show_map ) {
                        $map_facet = true;
                    }
                    break;
                default:
                    throw new \RuntimeException('Unknown facet query field!');
                    break;
                }
            }
        }

        //check if map facet is present
        if ( $show_map && !$map_facet ) {
            //add relevant facet
            $facetSet->createFacetField('cGeogname')
                ->setField('cGeogname');
        }
    }

    /**
     * Search existing query decorators
     *
     * @return void
     */
    private function _searchQueryDecorators()
    {
        $finder = new Finder();
        $finder->files()
            ->in(__DIR__.'/../Entity/SolariumQueryDecorator')
            ->depth('== 0')
            ->name('*.php');

        foreach ($finder as $file) {
            try {
                $reflection = new \ReflectionClass(
                    'Bach\HomeBundle\Entity\SolariumQueryDecorator\\'.
                    $file->getBasename(".php")
                );

                $expectedClass = 'Bach\HomeBundle\Entity' .
                    '\SolariumQueryDecoratorAbstract';
                $class = $reflection->getParentClass()->getName();
                if ( $expectedClass == $class ) {
                    $this->_registerQueryDecorator($reflection->newInstance());
                }
            } catch(\RuntimeException $e) {
            }
        }
    }

    /**
     * Get extreme dates from index stats
     *
     * @param array $filters Active filters
     *
     * @return array
     */
    public function getSliderDates($filters)
    {
        list($min_date, $max_date) = $this->_loadDatesFromStats();

        $results = array(
            'date_step_unit'    => null,
            'date_step'         => null,
            'min_date'          => null,
            'selected_min_date' => null,
            'max_date'          => null,
            'selected_max_date' => null
        );

        if ( $min_date && $max_date ) {
            $step_unit = 'years';
            $step = 1;

            $php_min_date = new \DateTime($min_date);
            $php_max_date = new \DateTime($max_date);

            $diff = $php_min_date->diff($php_max_date);
            if ( $diff->y > 100 ) {
                $step = $diff->y / 100;
            }

            $results['date_step_unit'] = $step_unit;
            $results['date_step'] = $step;

            $results['min_date'] = $php_min_date->format('Y');
            if ( isset($filters['cDateBegin']) ) {
                $dbegin = explode(
                    '-',
                    $filters['cDateBegin'][0]
                );
                $results['selected_min_date'] = $dbegin[0];
            } else {
                $results['selected_min_date'] = $results['min_date'];
            }
            $results['max_date'] = $php_max_date->format('Y');
            if ( isset($filters['cDateEnd']) ) {
                $dend = explode(
                    '-',
                    $filters['cDateEnd'][0]
                );
                $results['selected_max_date'] = $dend[0];
            } else {
                $results['selected_max_date'] = $results['max_date'];
            }

            $this->_max_low_date = $php_min_date;
            $this->_max_up_date = $php_max_date;

            return $results;
        }
    }

    /**
     * Get number of results per year, to draw plot
     *
     * @return array
     */
    public function getResultsByYear()
    {
        $query = $this->_client->createSelect();
        $query->setQuery('*:*');
        $query->setRows(0);
        $query->setFields('cDateBegin');

        $facetSet = $query->getFacetSet();
        $facetSet->setLimit(-1);

        list($min_date, $max_date) = $this->_loadDatesFromStats(true, true);
        $low_date = new \DateTime($min_date);
        $up_date = new \DateTime($max_date);

        $fr = $facetSet->createFacetRange('cDate');
        $fr->setField('cDateBegin');
        $fr->setStart($low_date->format('Y-01-01\T00:00:00\Z'));
        $fr->setgap('+1YEARS');
        $fr->setEnd($up_date->format('Y-01-01\T00:00:00\Z'));

        $rs = $this->_client->select($query);
        $facetSet = $rs->getFacetSet();
        $dates = $facetSet->getFacet('cDate');

        $results = array();
        foreach ( $dates as $d=>$count ) {
            $_date = new \DateTime($d);
            $results[] = array(
                (string)$_date->format('Y'),
                $count
            );
        }

        return $results;
    }

    /**
     * Retrieve GeoJSON informations
     *
     * @param Facet\Field      $map_facets Map facets
     * @param EntityRepository $repo       Entity repository
     * @param boolean          $zones      Load zones when possible
     *
     * @return string
     */
    public function getGeoJson(
        Field $map_facets, EntityRepository $repo, $zones = false
    ) {
        $result = null;
        $values = array();
        $parameters = array();

        if ( count($map_facets) > 0 ) {
            foreach ( $map_facets as $item=>$count ) {
                $values[$item] = $count;
            }

            $qb = $repo->createQueryBuilder('g');
            $qb->where('g.indexed_name IN (:names)');
            $parameters['names'] = array_keys($values);

            if ( $zones != false ) {
                list($swest_lon, $swest_lat, $neast_lon, $neast_lat) = explode(',', $zones);
                $qb
                    ->andWhere('g.lon BETWEEN :west_lon AND :east_lon')
                    ->andWhere('g.lat BETWEEN :north_lat AND :south_lat');

                $parameters = array_merge(
                    $parameters,
                    array(
                        'west_lon'  => (float)$swest_lon,
                        'east_lon'  => (float)$neast_lon,
                        'north_lat' => (float)$swest_lat,
                        'south_lat' => (float)$neast_lat
                    )
                );
            }

            $qb->setParameters($parameters);

            $query = $qb->getQuery();
            $polygons = $query->getResult();

            //prepare json
            if ( count($polygons) > 0 ) {
                $i = 1;
                $results = array();
                foreach ( $polygons as $polygon ) {
                    $id = $polygon->getId();
                    $name = $polygon->getIndexedName();

                    if ( !isset($values[$name]) ) {
                        //for MySQL, Vénéjan is the same as Venejan...
                        continue;
                    }

                    $count = $values[$name];
                    $json = $polygon->getGeojson();

                    $geometry = null;
                    if ( $zones !== false
                        && strlen($json) < 50000
                    ) {
                        $geometry = $json;
                    } else {
                        //polygon are too heavy, lets display a point instead
                        $lon = $polygon->getLon();
                        $lat = $polygon->getLat();
                        $geometry = str_replace(
                            array('%lon', '%lat'),
                            array($lon, $lat),
                            '{"type":"Point","coordinates":[%lon,%lat]}'
                        );
                    }

                    $results[] = str_replace(
                        '%geometry',
                        $geometry,
                        "\n" . '{"type": "Feature", "id":"' . $id .
                        '", "properties":{"name": "' . $name  . '", "results": ' .
                        $count . '}, "geometry": %geometry}'
                    );
                }
                if ( count($results) > 0 ) {
                    $result = '{"type": "FeatureCollection", "features":[';
                    $result .= implode(',', $results);
                    $result .= ']}';
                }
            }
        }

        return $result;
    }

    /**
     * Load dates bounds from index stats
     *
     * @param boolean $all   Use *:* as a query if true, use current query if false
     * @param boolean $begin Work only on begin date
     *
     * @return array
     */
    private function _loadDatesFromStats($all = true, $begin = false)
    {
        $query = $this->_client->createSelect();
        if ( $all === true ) {
            $query->setQuery('*:*');
        } else {
            $query = clone $this->_query;
        }
        $query->setRows(0);
        $stats = $query->getStats();

        $stats->createField('cDateBegin');
        $stats->createField('cDateEnd');

        $rs = $this->_client->select($query);
        $rsStats = $rs->getStats();
        $statsResults = $rsStats->getResults();

        $min_date = $statsResults['cDateBegin']->getMin();
        $max_date = null;
        if ( $begin === false ) {
            $max_date = $statsResults['cDateEnd']->getMax();
        } else {
            $max_date = $statsResults['cDateBegin']->getMax();
        }

        return array($min_date, $max_date);
    }

    /**
     * Register query decorator
     *
     * @param SolariumQueryDecoratorAbstract $decorator Decorator
     *
     * @return void
     */
    private function _registerQueryDecorator(
        SolariumQueryDecoratorAbstract $decorator
    ) {
        $this->_decorators[$decorator->getTargetField()] = $decorator;
    }

    /**
     * Get executed solr query
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get highlighted results
     *
     * @return Solarium\QueryType\Select\Result\Highlighting\Highlighting
     */
    public function getHighlighting()
    {
        return $this->_highlitght;
    }

    /**
     * Get spellcheck results
     *
     * @return Solarium\QueryType\Select\Result\Highlighting\Highlighting
     */
    public function getSpellcheck()
    {
        return $this->_spellcheck;
    }

    /**
     * Get query
     *
     * @return Solarium\Query\Select
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Set dates bounds
     *
     * @param array $filters Active filters
     *
     * @return void
     */
    public function setDatesBounds($filters)
    {
        list($low,$up) = $this->_getDates($filters);

        if ( !isset($this->_date_gap) ) {
            $this->_low_date = $low->format('Y-01-01') . 'T00:00:00Z';
            $this->_up_date = $up->format('Y-12-31') . 'T23:59:59Z';

            $diff = $low->diff($up);
            $gap = 1;
            if ( $diff->y > 10 ) {
                $gap = ceil($diff->y / 10);
            }
            $this->_date_gap = $gap;
        }
    }

    /**
     * Get dates bounds within query
     *
     * @param array $filters Active filters
     *
     * @return array
     */
    private function _getDates($filters)
    {
        list($min_date, $max_date) = $this->_loadDatesFromStats(false, true);
        if ( !isset($filters['cDate']) ) {
            $low = new \DateTime($min_date);
            $up = new \DateTime($max_date);
        } else {
            list($start, $end) = explode('|', $filters['cDate'][0]);
            $low = new \DateTime($start);
            $up = new \DateTime($end);
        }
        return array($low, $up);
    }

    /**
     * Get date gap
     *
     * @return int
     */
    public function getDateGap()
    {
        return $this->_date_gap;
    }
}
