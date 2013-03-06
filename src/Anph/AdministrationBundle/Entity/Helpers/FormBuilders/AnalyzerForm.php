<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Anph\AdministrationBundle\Entity\SolrSchema\BachAttribute;
use Anph\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AnalyzerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bachTagType = BachSchemaConfigReader::ANALYZER_TAG;
        $reader = new BachSchemaConfigReader();
        
        // Form attributes
        $builder->add('name', 'text', array(
                'label'    => 'Field type name',
                'disabled' => true
                ));
        $attr = $reader->getAttributeByTag($bachTagType, 'class');
        $builder->add('class', 'choice', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired(),
                'choices' => $this->retreiveClassAttributeValues($reader)));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Anph\AdministrationBundle\Entity\Helpers\FormObjects\Analyzer',
        ));
    }
    
    public function getName()
    {
        return 'analyzerType';
    }
    
    private function retreiveClassAttributeValues(BachSchemaConfigReader $reader)
    {
        $attr = $reader->getAttributeByTag(BachSchemaConfigReader::ANALYZER_TAG, 'class');
        return $attr->getValues();
    }
}
