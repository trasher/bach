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
        'cDate'
    );
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
     * Perform a query into Solr
     *
     * @param SolariumQueryContainer $container Solarium container
     * @param array                  $facets    Facets
     *
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function performQuery(SolariumQueryContainer $container, $facets)
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
                        /*$edate->add(new \DateInterval('P' . $this->_date_gap . 'Y'));
                        $edate->sub(new \DateInterval('PT1S'));*/
                        $this->_query->createFilterQuery($name)
                            ->setQuery(
                                '+cDateBegin:[' .
                                $bdate->format('Y-m-d\TH:i:s\Z') .
                                ' TO ' .
                                $edate->format('Y-m-d\TH:i:s\Z')  . ']'
                            );
                        /*$this->_date_gap = null;
                        $this->setDatesBounds(
                            $bdate->format('Y'),
                            $edate->format('Y')
                        );*/
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

        $facetSet = $this->_query->getFacetSet();
        $facetSet->setLimit(-1);
        $facetSet->setMinCount(1);

        //dynamically create facets
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
                    $fr = $facetSet->createFacetRange('cDate');
                    $fr->setField('cDateBegin');
                    $fr->setStart($this->_low_date);
                    $fr->setgap('+' . $this->_date_gap . 'YEARS');
                    $fr->setEnd($this->_up_date);
                    break;
                default:
                    throw new \RuntimeException('Unknown facet query field!');
                    break;
                }
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

        $this->_request = $this->_client->createRequest($this->_query);
        $rs = $this->_client->select($this->_query);
        $this->_highlitght = $rs->getHighlighting();
        $this->_spellcheck = $rs->getSpellcheck();
        return $rs;
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
     * @param string $low Low bound
     * @param string $up  Up bound
     *
     * @return void
     */
    public function setDatesBounds($low, $up)
    {
        if ( !isset($this->_date_gap) ) {
            $low = \DateTime::createFromFormat('Y', $low);
            $up = \DateTime::createFromFormat('Y', $up);

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
     * Get date gap
     *
     * @return int
     */
    public function getDateGap()
    {
        return $this->_date_gap;
    }
}
