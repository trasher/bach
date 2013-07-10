<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class TypesForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('types', 'collection', array(
                'type' => new FieldTypeForm(),
                'allow_add' => true));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\Types',
        ));
    }
    
    public function getName()
    {
        return 'typesForm';
    }
}
