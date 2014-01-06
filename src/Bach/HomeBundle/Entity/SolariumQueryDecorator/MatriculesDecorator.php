<?php
/**
 * Bach Solarium matricules decorator
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
 * Bach Solarium matricules decorator
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class MatriculesDecorator extends SolariumQueryDecoratorAbstract
{
    protected $targetField = "matricules";

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

    /**
     * Highlithed fields
     *
     * @return string
     */
    public function getHlFields()
    {
        return 'nom,prenoms,lieu_naissance,lieu_enregistrement';
    }
}
