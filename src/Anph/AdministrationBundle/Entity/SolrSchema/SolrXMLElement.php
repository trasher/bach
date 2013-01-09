<?php
namespace Anph\AdministrationBundle\Entity\SolrSchema;

use Doctrine\Common\Collections\ArrayCollection;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SolrXMLElement")
 */
class SolrXMLElement
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $SolrXMLElementID;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	protected $tag;

	/**
	 * @ORM\ManyToOne(targetEntity="SolrXMLFile", inversedBy="elements", cascade={"remove"})
	 * @ORM\JoinColumn(name="id", referencedColumnName="SolrXMLFileID")
	 */
	protected $file;

	/**
	 * @ORM\OneToMany(targetEntity="SolrXMLAttribute", mappedBy="Element", cascade={"remove", "persist"})
	 */
	protected $attributes;

	
	/**
	 * @ORM\OneToMany(targetEntity="SolrXMLElement", mappedBy="SolrXMLElementID", cascade={"remove", "persist"})
	 */
	 protected $elements;

	/**
	 * @ORM\ManyToOne(targetEntity="SolrXMLElement", inversedBy="elements", cascade={"remove"})
	 * @ORM\JoinColumn(name="root", referencedColumnName="SolrXMLElementID")
	 */
	protected $root;


	/**
	 * @ORM\Column(type="text")
	 */
	protected $value;



	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->elements = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get SolrXMLElementID
     *
     * @return integer 
     */
    public function getSolrXMLElementID()
    {
        return $this->SolrXMLElementID;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return SolrXMLElement
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return SolrXMLElement
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set file
     *
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLFile $file
     * @return SolrXMLElement
     */
    public function setFile(\Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLFile $file = null)
    {
        $this->file = $file;
    
        return $this;
    }

    /**
     * Get file
     *
     * @return \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLFile 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Add attributes
     *
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute $attributes
     * @return SolrXMLElement
     */
    public function addAttribute(\Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute $attributes)
    {
        $this->attributes[] = $attributes;
    
        return $this;
    }

    /**
     * Remove attributes
     *
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute $attributes
     */
    public function removeAttribute(\Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute $attributes)
    {
        $this->attributes->removeElement($attributes);
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add elements
     *
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $elements
     * @return SolrXMLElement
     */
    public function addElement(\Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $elements)
    {
        $this->elements[] = $elements;
    
        return $this;
    }

    /**
     * Remove elements
     *
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $elements
     */
    public function removeElement(\Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $elements)
    {
        $this->elements->removeElement($elements);
    }

    /**
     * Get elements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Set root
     *
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $root
     * @return SolrXMLElement
     */
    public function setRoot(\Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $root = null)
    {
        $this->root = $root;
    
        return $this;
    }

    /**
     * Get root
     *
     * @return \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement 
     */
    public function getRoot()
    {
        return $this->root;
    }
}