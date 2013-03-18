<?php 

namespace Anph\HomeBundle\Entity\SolariumQueryDecorator;

use Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

class ArchDescUnitTitleDecorator extends SolariumQueryDecoratorAbstract
{
	protected $_targetField = "archDescUnitTitle";

	public function decorate(\Solarium_Query_Select $query, $data){
		$data = strip_tags(str_replace("*", "", $data));
		$query->createFilterQuery('archDescUnitTitle')->setQuery('archDescUnitTitle:*'.$data.'*');
		// get the facetset component
		/*$facetSet = $query->getFacetSet();

		// create a facet field instance and set options
		$facetSet->createFacetField('title')->setField('archDescUnitTitle');*/	
	}
}