<?php
/**
 * Bach Solarium pager decorator
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity\SolariumQueryDecorator;

use Bach\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

/**
 * Bach Solarium pager decorator
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class PagerDecorator extends SolariumQueryDecoratorAbstract
{
    protected $targetField = "pager";

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
        $query->setStart($data["start"])->setRows($data["offset"]);
    }
}
