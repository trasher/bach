<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormBuilders;

use Anph\AdministrationBundle\Entity\SolrSchema\BachAttribute;
use Anph\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CopyFieldForm
{
    const TYPE = 'copyField';
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bachTagType = BachSchemaConfigReader::COPY_FIELD_TAG;
        $reader = new BachSchemaConfigReader();
        $attr = $reader->getAttributeByTag($bachTagType, 'source');
        $builder->add('source', 'text', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired()));
        $attr = $reader->getAttributeByTag($bachTagType, 'dest');
        $builder->add('dest', 'text', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired(),
                'choices' => $this->retreiveTypeAttributeValues()));
        $attr = $reader->getAttributeByTag($bachTagType, 'maxChars');
        $builder->add('maxChars', 'integer', array(
                'label' => $attr->getLabel(),
                'required' => $attr->isRequired()));
    }
    
    public function getName()
    {
        return self::TYPE;
    }
}
