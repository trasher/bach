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
}