<?php

namespace Anph\HomeBundle\Entity\SolariumQueryDecorator;

use Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

class PagerDecorator extends SolariumQueryDecoratorAbstract
{
    protected $targetField = "pager";

    public function decorate(\Solarium\QueryType\Select\Query\Query $query, $data){
        $query->setStart($data["start"])->setRows($data["offset"]);
    }
}
