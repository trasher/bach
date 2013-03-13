<?php 

namespace Anph\HomeBundle\Entity\Sidebar;

use Symfony\Component\HttpFoundation\Request;

class OptionSidebar
{
	private $items = array();
	
	public function append(OptionSidebarItem $item){
		$this->items[] = $item;
		
		return $this;
	}
	
	public function getItems(){
		return $this->items;
	}
	
	public function bind(Request $request){
		foreach($this->items as $item){
			foreach($item->getChoices() as $choice){
				$get = $request->get($item->getKey(),$item->getDefault(),false);
					
				if($get == $choice->getValue()){
					$choice->setSelected(true);
				}
			}
		}
	}
}

?>