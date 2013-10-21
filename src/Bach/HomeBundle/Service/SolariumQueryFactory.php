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
use Bach\HomeBundle\Entity\Facets;

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
                        ->setQuery('+' . $name . ':[' . $v . ' TO *]');
                } else if ( $name === 'cDateEnd' ) {
                    //no $i in name here since we only want ONE begin 
                    //and end date filter
                    $this->_query->createFilterQuery($name)
                        ->setQuery('+' . $name . ':[* TO ' . $v . ']');
                } else {
                    $this->_query->createFilterQuery($name . $i)
                        ->setQuery('+' . $name . ':"' . $v . '"');
                }
                $i++;
            }
        }

        if ( $container->isIllustrated() ) {
            $this->_query->createFilterQuery('illustrated')
                ->setQuery('+dao:*');
        }

        if ( $container->isOrdered() ) {
            $qry = $this->_query;
            $order = $container->getOrderField();
            if ( is_array($order) ) {
                foreach ( $order as $field) {
                    $this->_query->addSort(
                        $field,
                        ($container->getOrderDirection() === ViewParams::ORDER_ASC) ? $qry::SORT_ASC : $qry::SORT_DESC
                    );
                }
            } else {
                $this->_query->addSort(
                    $container->getOrderField(),
                    ($container->getOrderDirection() === ViewParams::ORDER_ASC) ? $qry::SORT_ASC : $qry::SORT_DESC
                );
            }
        }

        $facetSet = $this->_query->getFacetSet();
        $facetSet->setLimit(-1);
        $facetSet->setMinCount(1);

        //dynamically create facets
        foreach ( $facets as $facet ) {
            $facetSet->createFacetField($facet->getSolrFieldName())
                ->setField($facet->getSolrFieldName());
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
}
