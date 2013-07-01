<?php 
/**
 * Bach Solarium fulltext decorator
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Anph\HomeBundle\Entity\SolariumQueryDecorator;

use Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

/**
 * Bach Solarium fulltext decorator
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class FulltextDecorator extends SolariumQueryDecoratorAbstract
{
    protected $targetField = "fulltext";

    /**
     * Decorat Query
     *
     * @param Query  $query Solarium query object to decorate
     * @param string $data  Query data
     *
     * @return void
     */
    public function decorate(\Solarium\QueryType\Select\Query\Query $query, $data)
    {
        $query->setQuery('fulltext:' . $data);
    }
}