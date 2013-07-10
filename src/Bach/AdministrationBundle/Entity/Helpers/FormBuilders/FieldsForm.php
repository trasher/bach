<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class FieldsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fields', 'collection', array(
                'type' => new FieldForm(),
                'allow_add' => true));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\Fields',
        ));
    }
    
    public function getName()
    {
        return 'fieldsForm';
    }
}
