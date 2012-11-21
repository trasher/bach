<?php

namespace Anph\IndexationBundle\Entity\Mapper;

use Anph\IndexationBundle\DriverMapperInterface;
use Anph\IndexationBundle\Entity\KeyTranslator;

class EADDriverMapper implements DriverMapperInterface
{
    public function translate($data){
    	$translator = new KeyTranslator($data);
    	//$translator->addTranslation('did/unittitle','unittitle');
    	return $translator->translate();
    }
}
