<?php
/**
 * Bach core creation form
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Bach core creation form
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CoreCreationForm extends AbstractType
{
    private $_tables;

    /**
     * Main Constructor
     *
     * @param array $tables Existing tables
     */
    public function __construct($tables)
    {
        $this->_tables = $tables;
    }

    /**
     * Build the form
     *
     * @param FormBuilderInterface $builder Builder interface
     * @param array                $options Options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'core',
            'choice',
            array(
                'required' => true,
                'choices'  => $this->_tables
            )
        )->add(
            'name',
            'text',
            array(
                'required'  => true
            )
        );
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver Resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\CoreCreation',
            )
        );
    }

    /**
     * Get form name
     *
     * @return String
     */
    public function getName()
    {
        return 'coreCreationForm';
    }
}
