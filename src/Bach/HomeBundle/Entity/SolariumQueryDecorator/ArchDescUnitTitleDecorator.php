<?php
/**
 * Bach Solarium archdesc title decorator
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
 * Bach Solarium archdesc title decorator
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class ArchDescUnitTitleDecorator extends SolariumQueryDecoratorAbstract
{
    protected $targetField = "archDescUnitTitle";

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
        $data = strip_tags(str_replace("*", "", $data));
        $query->createFilterQuery('archDescUnitTitle')
            ->setQuery('archDescUnitTitle:*'.$data.'*');
    }
}
