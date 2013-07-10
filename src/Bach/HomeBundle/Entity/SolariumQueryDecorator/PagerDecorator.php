<?php

namespace Bach\HomeBundle\Entity\SolariumQueryDecorator;

use Bach\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

class PagerDecorator extends SolariumQueryDecoratorAbstract
{
    protected $targetField = "pager";

    public function decorate(\Solarium\QueryType\Select\Query\Query $query, $data){
        $query->setStart($data["start"])->setRows($data["offset"]);
    }
}
