<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Bach\AdministrationBundle\Entity\SolrSchema\BachAttribute;
use Bach\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
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
                'read_only' => true
                ));
        $attr = $reader->getAttributeByTag($bachTagType, 'class');
        $builder->add('class', 'choice', array(
                'label' => $attr->getLabel(),
                'empty_value' => '<-- Aucun -->',
                'required' => false,
                'choices' => $this->retreiveClassAttributeValues($reader)));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\Analyzer',
        ));
    }
    
    public function getName()
    {
        return 'analyzerTypeForm';
    }
    
    private function retreiveClassAttributeValues(BachSchemaConfigReader $reader)
    {
        $attr = $reader->getAttributeByTag(BachSchemaConfigReader::ANALYZER_TAG, 'class');
        return $attr->getValues();
    }
}
