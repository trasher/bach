<?php
/**
 * Bach abstract solarium query decorator
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity;

use Solarium\QueryType\Select\Query\Query;

/**
 * Bach abstract solarium query decorator
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
abstract class SolariumQueryDecoratorAbstract
{

    /**
     * Get target field
     *
     * @return string
     */
    public function getTargetField()
    {
        return $this->targetField;
    }

    /**
     * Decorate query
     *
     * @param Query $query Solarium query to decorate
     * @param array $data  Query data
     *
     * @return void
     */
    abstract public function decorate(Query $query, $data);
}
?>
