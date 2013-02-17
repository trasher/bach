<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Anph\AdministrationBundle\Entity\SolrSchema\BachAttribute;
use Anph\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CopyFieldForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bachTagType = BachSchemaConfigReader::COPY_FIELD_TAG;
        $reader = new BachSchemaConfigReader();
        
        // Form attributes
        $attr = $reader->getAttributeByTag($bachTagType, 'source');
        $builder->add('source', 'text', array(
                'label'    => $attr->getLabel(),
                'required' => $attr->isRequired()
                ));
        $attr = $reader->getAttributeByTag($bachTagType, 'dest');
        $builder->add('dest', 'text', array(
                'label'    => $attr->getLabel(),
                'required' => $attr->isRequired()
                ));
        $attr = $reader->getAttributeByTag($bachTagType, 'maxChars');
        $builder->add('maxChars', 'integer', array(
                'label'    => $attr->getLabel(),
                'required' => $attr->isRequired()
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Anph\AdministrationBundle\Entity\Helpers\FormObjects\CopyField',
        ));
    }
    
    public function getName()
    {
        return 'copyField';
    }
}
