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
		
		foreach($items as $item){
			$output[$item->getName()] = array();
			
			foreach($item->getChoices() as $choice){
				
				if($choice->isSelected()){
					$linkValues[$item->getKey()] = $choice->getValue();
				}
				
				$output[$item->getName()][] = array(	"alias"		=>	$choice->getAlias(),
														"key"		=>	$item->getKey(),
														"value"		=>	$choice->getValue(),
														"selected"	=>	$choice->isSelected());
			}
		}
		
		
		$urlParams = "";
		
		foreach($output as $itemName => $item){
			foreach($item as $key=>$choice){				
				$url = "";
				foreach($linkValues as $linkKey=>$linkValue){
					$url .= $linkKey."=";
					
					if($linkKey == $choice["key"]){
						$url .= $choice["value"];
					}else{
						$url .= $linkValue;
					}
					
					$url .= "&";
				}
				$url = substr($url,0,strlen($url)-1);
				
				$output[$itemName][$key]["url"] = $url;				
			}
		}
		
		return $output;
	}
}

?>