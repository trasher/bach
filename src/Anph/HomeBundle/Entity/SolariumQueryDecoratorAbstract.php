<?php

namespace Anph\HomeBundle\Entity;

abstract class SolariumQueryDecoratorAbstract
{
    public function getTargetField(){
        return $this->targetField;
    }

    abstract public function decorate(\Solarium\QueryType\Select\Query\Query $query, $data);
}
?>
