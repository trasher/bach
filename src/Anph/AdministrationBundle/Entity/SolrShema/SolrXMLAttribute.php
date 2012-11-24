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
	 * @ORM\ManyToOne(targetEntity="SolrXMLElement", inversedBy="attributes", cascade={"remove"})
	 * @ORM\JoinColumn(name="SolrXMLElementID", referencedColumnName="SolrXMLElementID")
	 */
	protected $Element;

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
    

    /**
     * Set Element
     *
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $element
     * @return SolrXMLAttribute
     */
    public function setElement(\Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $element = null)
    {
        $this->Element = $element;
    
        return $this;
    }

    /**
     * Get Element
     *
     * @return \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement 
     */
    public function getElement()
    {
        return $this->Element;
    }
}