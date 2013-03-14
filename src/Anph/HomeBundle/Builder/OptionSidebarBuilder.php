<?php 

namespace Anph\HomeBundle\Builder;

use Anph\HomeBundle\Entity\Sidebar\OptionSidebar;

class OptionSidebarBuilder
{
	private $sidebar;
	
	public function __construct(OptionSidebar $sidebar){
		$this->sidebar = $sidebar;
	}
	
	public function compileToArray(){
		$output = array();
		
		$items = $this->sidebar->getItems();
		
		$linkValues = array();
		
		$urlParams = $this->sidebar->getRequest()->query->all();
		
		foreach($items as $item){
			$output[$item->getName()] = array();
			$tempUrlParams = $urlParams;
			
			foreach($item->getChoices() as $choice){
				
				if($choice->isSelected()){
					$tempUrlParams[$item->getKey()] = $choice->getValue();
				}else{
					$tempUrlParams[$item->getKey()] = $choice->getValue();
				}
				
				$output[$item->getName()][] = array(	"alias"		=>	$choice->getAlias(),
														"key"		=>	$item->getKey(),
														"value"		=>	$choice->getValue(),
														"selected"	=>	$choice->isSelected(),
														"url"		=>	$this->sidebar->getRequest()->getBaseUrl()."?".http_build_query($tempUrlParams));
			}
		}
		
		
		return $output;
	}
}

?>