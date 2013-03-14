<?php 

namespace Anph\HomeBundle\Entity\Sidebar;

use Symfony\Component\HttpFoundation\Request;

class OptionSidebar
{
	private $items = array();
	
	private $request = null;
	
	public function append(OptionSidebarItem $item){
		$this->items[] = $item;
		
		return $this;
	}
	
	/**
	 * @return \Symfony\Component\HttpFoundation\Request request
	 */
	public function getRequest(){
		return $this->request;
	}
	
	public function getItems(){
		return $this->items;
	}
	
	public function getItemValue($key){
		foreach($this->items as $item){
			if($item->getKey() == $key){
				foreach($item->getChoices() as $choice){
					if($choice->isSelected()){
						return $choice->getValue();
					}
				}
				return null;
			}
		}
		
		return null;
	}
	
	public function bind(Request $request){
		$this->request = $request;
		
		foreach($this->items as $item){
			$found = false;
			foreach($item->getChoices() as $choice){
				$get = $request->query->get($item->getKey(),$item->getDefault(),false);
					
				if($get == $choice->getValue()){
					$found = true;
					$choice->setSelected(true);
				}
			}
			
			if(!$found){
				$choices = $item->getChoices();
				$choices[$item->getDefault()]->setSelected(true);
			}
		}
	}
}

?>