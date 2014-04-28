<?php
/**
 * Bach Solarium pmb decorator
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity\SolariumQueryDecorator;

use Bach\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

/**
 * Bach Solarium pmb decorator
 *
 * @category Search
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class PMBDecorator extends SolariumQueryDecoratorAbstract
{
    protected $targetField = 'pmb';

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
            /*$dismax = $query->getDisMax();
            $dismax->setQueryFields(
                'txt_nom^2 txt_prenoms lieu_naissance lieu_enregistrement fulltext^0.1'
            );*/
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
        return '';
    }
}
