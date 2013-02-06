<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormBuilders;

use Anph\AdministrationBundle\Entity\SolrSchema\BachAttribute;
use Anph\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AnalyzerForm
{
    const TYPE = 'analyzerType';
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bachTagType = BachSchemaConfigReader::ANALYZER_TAG;
        $reader = new BachSchemaConfigReader();
        $attr = $reader->getAttributeByTag($bachTagType, 'class');
        $builder->add('class', 'choice', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired(),
                'choices' => $this->retreiveClassAttributeValues($reader)));
    }
    
    public function getName()
    {
        return self::TYPE;
    }
    
    private function retreiveClassAttributeValues(BachSchemaConfigReader $reader)
    {
        $attr = $reader->getAttributeByTag(BachSchemaConfigReader::ANALYZER_TAG, 'class');
        return $attr->getValues();
    }
}
