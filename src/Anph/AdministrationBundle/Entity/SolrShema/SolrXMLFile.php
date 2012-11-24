<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;

	
	use Doctrine\ORM\Mapping as ORM;
	
	/**
	 * @ORM\Entity
	 * @ORM\Table(name="SolrXMLFile")
	 */
	class SolrXMLFile
	{
		/**
		 * @ORM\Id
		 * @ORM\Column(type="integer")
		 * @ORM\GeneratedValue(strategy="AUTO")
		 */
		protected $SolrXMLFileID;
	
		/**
		 * @ORM\Column(type="string", length=100)
		 */
		protected $name;
		
		/**
		 * @ORM\OneToMany(targetEntity="SolrXMLElement", mappedBy="SolrXMLFile", cascade={"remove", "persist"})
		 */
		protected $elements;
	
		/**
		 * @ORM\Column(type="text")
		 */
		protected $path;
	
    /**
     * Get SolrXMLFileID
     *
     * @return integer 
     */
    public function getSolrXMLFileID()
    {
        return $this->SolrXMLFileID;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SolrXMLFile
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
     * Set path
     *
     * @param string $path
     * @return SolrXMLFile
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->solrXMLElement = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add solrXMLElement
     *
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $solrXMLElement
     * @return SolrXMLFile
     */
    public function addSolrXMLElement(\Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $solrXMLElement)
    {
        $this->solrXMLElement[] = $solrXMLElement;
    
        return $this;
    }

    /**
     * Remove solrXMLElement
     *
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $solrXMLElement
     */
    public function removeSolrXMLElement(\Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $solrXMLElement)
    {
        $this->solrXMLElement->removeElement($solrXMLElement);
    }

    /**
     * Get solrXMLElement
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSolrXMLElement()
    {
        return $this->solrXMLElement;
    }

    /**
     * Add elements
     *
     * @param \Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement $elements
     * @return SolrXMLFile
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
}