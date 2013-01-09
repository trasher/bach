<?php
namespace Anph\AdministrationBundle\Entity\SolrSchema;


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
	protected $element;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	protected $name;

	/**
	 * @ORM\Column(type="text")
	 */
	protected $value;


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
     * Set attribute's name
     *
     * @param string $name
     * @return SolrXMLAttribute
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
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
     * @param string $value
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
    

    /**
     * Set element
     *
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $element
     * @return SolrXMLAttribute
     */
    public function setElement(\Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $element = null)
    {
        $this->element = $element;
    
        return $this;
    }

    /**
     * Get element
     *
     * @return \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement 
     */
    public function getElement()
    {
        return $this->element;
    }
}