<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Anph\AdministrationBundle\Entity\SolrSchema\BachAttribute;
use Anph\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\FormBuilderInterface;

class FieldTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bachTagType = BachSchemaConfigReader::FIELD_TYPE_TAG;
        $reader = new BachSchemaConfigReader();
        
        // Form attributes
        $attr = $reader->getAttributeByTag($bachTagType, 'name');
        $builder->add('name', 'text', array(
                'label'    => $attr->getLabel(),
                'required' => $attr->isRequired()
                ));
        $attr = $reader->getAttributeByTag($bachTagType, 'class');
        $builder->add('class', 'choice', array(
                'label'    => $attr->getLabel(),
                'required' => $attr->isRequired(),
                'choices'  => $this->retreiveClassAttributeValues($reader)
                ));
        $attr = $reader->getAttributeByTag($bachTagType, 'sortMissingLast');
        $builder->add('sortMissingLast', 'checkbox', array(
                'label'    => $attr->getLabel(),
                'required' => $attr->isRequired()
                ));
        $attr = $reader->getAttributeByTag($bachTagType, 'sortMissingFirst');
        $builder->add('sortMissingFirst', 'checkbox', array(
                'label'    => $attr->getLabel(),
                'required' => $attr->isRequired()
                ));
        $attr = $reader->getAttributeByTag($bachTagType, 'positionIncrementGap');
        $builder->add('positionIncrementGap', 'integer', array(
                'label'    => $attr->getLabel(),
                'required' => $attr->isRequired()));
        $attr = $reader->getAttributeByTag($bachTagType, 'autoGeneratePhraseQueries');
        $builder->add('autoGeneratePhraseQueries', 'checkbox', array(
                'label'    => $attr->getLabel(),
                'required' => $attr->isRequired()
                ));
        
        // Other Attributes that can be added to the application in the future
        /*$attr = $reader->getAttributeByTag($bachTagType, 'indexed');
        $builder->add('indexed', 'checkbox', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired()));
        $attr = $reader->getAttributeByTag($bachTagType, 'stored');
        $builder->add('stored', 'checkbox', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired()));
        $attr = $reader->getAttributeByTag($bachTagType, 'multiValued');
        $builder->add('multiValued', 'checkbox', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired()));
        $attr = $reader->getAttributeByTag($bachTagType, 'omitNorms');
        $builder->add('omitNorms', 'checkbox', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired()));
        $attr = $reader->getAttributeByTag($bachTagType, 'omitTermFreqAndPositions');
        $builder->add('omitTermFreqAndPositions', 'checkbox', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired()));
        $attr = $reader->getAttributeByTag($bachTagType, 'omitPositions');
        $builder->add('omitPositions', 'checkbox', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired()));*/
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Anph\AdministrationBundle\Entity\Helpers\FormObjects\FieldType',
        ));
    }
    
    public function getName()
    {
        return 'fieldType';
    }
    
    private function retreiveClassAttributeValues(BachSchemaConfigReader $reader)
    {
        $attr = $reader->getAttributeByTag(BachSchemaConfigReader::FIELD_TYPE_TAG, 'class');
        return $attr->getValues();
    }
}
