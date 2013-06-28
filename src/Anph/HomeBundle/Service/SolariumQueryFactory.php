<?php

namespace Anph\HomeBundle\Service;

use Symfony\Component\Finder\Finder;
use Anph\HomeBundle\Entity\SolariumQueryContainer;
use Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract;
use Anph\IndexationBundle\Exception\BadInputFileFormatException;
use Anph\IndexationBundle\Exception\UnknownDriverParserException;
use Doctrine\ORM\EntityManager;

class SolariumQueryFactory
{
    private $_client;
    private $_decorators = array();

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

        foreach ( $container->getFields() as $name=>$value ) {
            if ( array_key_exists($name, $this->_decorators) ) {
                //Decorate the query
                $this->_decorators[$name]->decorate($query, $value);
            }
        }

        return $this->_client->select($query);
    }

    /**
     * Search existing query decorators
     *
     * @return void
     */
    private function _searchQueryDecorators()
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../Entity/SolariumQueryDecorator')->depth('== 0')->name('*.php');

        foreach ($finder as $file) {
            try {
                $reflection = new \ReflectionClass(
                    'Anph\HomeBundle\Entity\SolariumQueryDecorator\\'.
                    $file->getBasename(".php")
                );

                if ('Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract' == $reflection->getParentClass()->getName()) {
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
}
