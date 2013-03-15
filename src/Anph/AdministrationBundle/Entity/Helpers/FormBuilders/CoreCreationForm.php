<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class CoreCreationForm extends AbstractType
{
    private $tables;
    
    public function __construct($tables)
    {
        $this->tables = $tables;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Form attributes
        $builder->add('name', 'text', array(
                'label'    => 'Nom',
                'required' => true
                ));
        $builder->add('repository', 'text', array(
                'label'    => 'Nom du répértoire',
                'required' => true
        ));
        $builder->add('table', 'choice', array(
                'label'    => 'Type de données',
                'required' => true,
                'choices'  => $this->tables
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
