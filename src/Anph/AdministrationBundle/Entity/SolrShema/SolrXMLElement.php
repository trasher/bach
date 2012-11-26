<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;

	
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
		protected $balise;
		
		/**
		 * @ORM\ManyToOne(targetEntity="SolrXMLFile", inversedBy="elements", cascade={"remove"})
		 * @ORM\JoinColumn(name="SolrXMLFileID", referencedColumnName="SolrXMLFileID")
		 */
		protected $file;
		
		/**
		 * @ORM\OneToMany(targetEntity="SolrXMLAttribute", mappedBy="SolrXMLElement", cascade={"remove", "persist"})
		 */
		protected $attributes;
		
		/**
		 * @ORM\OneToMany(targetEntity="SolrXMLElement", mappedBy="SolrXMLElement", cascade={"remove", "persist"})
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
     * Get SolrXMLElementID
     *
     * @return integer 
     */
    public function getSolrXMLElementID()
    {
        return $this->SolrXMLElementID;
    }

    /**
     * Set balise
     *
     * @param string $balise
     * @return SolrXMLElement
     */
    public function setBalise($balise)
    {
        $this->balise = $balise;
    
        return $this;
    }

    /**
     * Get balise
     *
     * @return string 
     */
    public function getBalise()
    {
        return $this->balise;
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
     * Constructor
     */
    public function __construct()
    {
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set file
     *
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLFile $file
     * @return SolrXMLElement
     */
    public function setFile(\Anph\AdministrationBundle\Entity\SolrShema\SolrXMLFile $file = null)
    {
        $this->file = $file;
    
        return $this;
    }

    /**
     * Get file
     *
     * @return \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLFile 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Add attributes
     *
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLAttribute $attributes
     * @return SolrXMLElement
     */
    public function addAttribute(\Anph\AdministrationBundle\Entity\SolrShema\SolrXMLAttribute $attributes)
    {
        $this->attributes[] = $attributes;
    
        return $this;
    }

    /**
     * Remove attributes
     *
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLAttribute $attributes
     */
    public function removeAttribute(\Anph\AdministrationBundle\Entity\SolrShema\SolrXMLAttribute $attributes)
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
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $elements
     * @return SolrXMLElement
     */
    public function addElement(\Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $elements)
    {
        $this->elements[] = $elements;
    
        return $this;
    }

    /**
     * Remove elements
     *
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $elements
     */
    public function removeElement(\Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $elements)
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
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $root
     * @return SolrXMLElement
     */
    public function setRoot(\Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $root = null)
    {
        $this->root = $root;
    
        return $this;
    }

    /**
     * Get root
     *
     * @return \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement 
     */
    public function getRoot()
    {
        return $this->root;
    }
}