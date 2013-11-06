<?php
/**
 * Bach solr XML attribute
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\SolrSchema;

/**
 * Bach solr XML attribute
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SolrXMLAttribute
{
    protected $name;
    protected $value;

    /**
     * Instanciate XML Attribute
     *
     * @param string $name  Attribute name
     * @param string $value Attribute value
     *
     * @return void
     */
    public function __construct($name, $value = null)
    {
        if ( $name == null ) {
            throw new \RuntimeException(
                'SolrXMLAttribute must be instanciated with a name!'
            );
        }
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set attribute's value
     *
     * @param string $value Value
     *
     * @return SolrXMLAttribute
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get attribute's value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
