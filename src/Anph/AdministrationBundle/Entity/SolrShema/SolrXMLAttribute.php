<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SolrXMLAttribute")
 */
class SolrXMLAttribute
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $SolrXMLAttributeID;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	protected $attributeName;

	/**
	 * @ORM\Column(type="text")
	 */
	protected $attributeValue;


    /**
     * Get SolrXMLAttributeID
     *
     * @return integer 
     */
    public function getSolrXMLAttributeID()
    {
        return $this->SolrXMLAttributeID;
    }

    /**
     * Set attributeName
     *
     * @param string $attributeName
     * @return SolrXMLAttribute
     */
    public function setAttributeName($attributeName)
    {
        $this->attributeName = $attributeName;
    
        return $this;
    }

    /**
     * Get attributeName
     *
     * @return string 
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     * Set attributeValue
     *
     * @param string $attributeValue
     * @return SolrXMLAttribute
     */
    public function setAttributeValue($attributeValue)
    {
        $this->attributeValue = $attributeValue;
    
        return $this;
    }

    /**
     * Get attributeValue
     *
     * @return string 
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }
}