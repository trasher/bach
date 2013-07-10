<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class AnalyzersForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('analyzers', 'collection', array(
                'type' => new AnalyzerForm(),
                'allow_add' => true));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\Analyzers',
        ));
    }

    public function getName()
    {
        return 'analyzersForm';
    }
}
