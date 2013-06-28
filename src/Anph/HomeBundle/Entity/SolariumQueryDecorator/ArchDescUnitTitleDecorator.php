<?php 

namespace Anph\HomeBundle\Entity\SolariumQueryDecorator;

use Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

class ArchDescUnitTitleDecorator extends SolariumQueryDecoratorAbstract
{
    protected $targetField = "archDescUnitTitle";

    public function decorate(\Solarium\QueryType\Select\Query\Query $query, $data)
    {
        $data = strip_tags(str_replace("*", "", $data));
        $query->createFilterQuery('archDescUnitTitle')->setQuery('archDescUnitTitle:*'.$data.'*');
    }
}
