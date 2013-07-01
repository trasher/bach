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

namespace Anph\HomeBundle\Service;

use Symfony\Component\Finder\Finder;
use Anph\HomeBundle\Entity\SolariumQueryContainer;
use Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract;
use Anph\IndexationBundle\Exception\BadInputFileFormatException;
use Anph\IndexationBundle\Exception\UnknownDriverParserException;
use Doctrine\ORM\EntityManager;

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
     *
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function performQuery(SolariumQueryContainer $container)
    {
        $query = $this->_client->createSelect();

        $hl = $query->getHighlighting();
        $hl_fields = '';

        foreach ( $container->getFields() as $name=>$value ) {
            if ( array_key_exists($name, $this->_decorators) ) {
                //Decorate the query
                $this->_decorators[$name]->decorate($query, $value);
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

        $this->_request = $this->_client->createRequest($query);
        $rs = $this->_client->select($query);
        $this->_highlitght = $rs->getHighlighting();
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
                    'Anph\HomeBundle\Entity\SolariumQueryDecorator\\'.
                    $file->getBasename(".php")
                );

                $expectedClass = 'Anph\HomeBundle\Entity' .
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
}
