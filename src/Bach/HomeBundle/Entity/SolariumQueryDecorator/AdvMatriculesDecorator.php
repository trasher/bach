<?php
/**
 * Bach Solarium matricules advanced decorator
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

/**
 * Bach Solarium matricules advanced decorator
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class AdvMatriculesDecorator extends MatriculesDecorator
{
    protected $targetField = 'adv_matricules';

    /**
     * Decorate Query
     *
     * @param Query $query Solarium query object to decorate
     * @param array $data  Query data
     *
     * @return void
     */
    public function decorate(\Solarium\QueryType\Select\Query\Query $query, $data)
    {
        $qry = '';
        foreach ( $data as $key=>$value ) {
            if ( $value !== null && trim($value !== '') ) {
                $qry .= '+' . $key . ':' . $value;
            }
        }
        $query->setQuery($qry);
    }
}
