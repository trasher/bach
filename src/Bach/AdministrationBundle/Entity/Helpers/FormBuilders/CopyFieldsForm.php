<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class CopyFieldsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('copyFields', 'collection', array(
                'type' => new CopyFieldForm(),
                'allow_add' => true));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\CopyFields',
        ));
    }

    public function getName()
    {
        return 'copyFieldsForm';
    }
}
