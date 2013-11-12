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

namespace Bach\HomeBundle\Entity\SolariumQueryDecorator;

use Bach\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

/**
 * Bach Solarium main decorator
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
        if ( $data !== '*:*' ) {
            $dismax = $query->getDisMax();
            $dismax->setQueryFields(
                'descriptors^2 tcUnittitle^1 parents_titles^1 fulltext^0.1'
            );
        }
        $query->setQuery($data);
    }

    /**
     * Highlithed fields
     *
     * @return string
     */
    public function getHlFields()
    {
        return 'cUnittitle';
    }
}
