<?php 
/**
 * Bach Solarium main decorator
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
 * Bach Solarium main decorator
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class MainDecorator extends SolariumQueryDecoratorAbstract
{
    protected $targetField = "main";

    /**
     * Decorate Query
     *
     * @param Query  $query Solarium query object to decorate
     * @param string $data  Query data
     *
     * @return void
     */
    public function decorate(\Solarium\QueryType\Select\Query\Query $query, $data)
    {
        $dismax = $query->getDisMax();
        /*$dismax->setBoostQuery(
            'cUnittitle:"' . $data . '"^100 fulltext:"' . $data . '"^0.2'
        );*/
        $dismax->setQueryFields('cUnittitle^2 fulltext^0.2');
        $query->setQuery($data);
    }
}
