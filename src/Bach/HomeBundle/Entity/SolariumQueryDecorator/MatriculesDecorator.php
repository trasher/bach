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
    protected $targetField = 'matricules';

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
                'txt_nom^2 txt_prenoms lieu_naissance lieu_enregistrement fulltext^0.1'
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
        return 'txt_nom,txt_prenoms,lieu_naissance,lieu_enregistrement';
    }
}
