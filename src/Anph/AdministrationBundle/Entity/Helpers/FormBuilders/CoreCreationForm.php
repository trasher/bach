<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormBuilders;

class CoreCreationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Form attributes
        $builder->add('name', 'text', array(
                'label'    => 'Nom',
                'required' => true
                ));
        $builder->add('name', 'text', array(
                'label'    => 'Nom du répértoire',
                'required' => true
        ));
        $builder->add('type', 'choice', array(
                'label'    => 'Type de données',
                'required' => true,
                'choices'  => $options,
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Anph\AdministrationBundle\Entity\Helpers\FormObjects\CoreCreation',
        ));
    }

    public function getName()
    {
        return 'coreCreation';
    }
}