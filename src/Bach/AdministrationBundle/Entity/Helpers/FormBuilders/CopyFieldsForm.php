<?php
/**
 * Copy fields form
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
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

/**
 * Copy fields form
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CopyFieldsForm extends AbstractType
{
    private $_xmlp;

    /**
     * Main constructor
     *
     * @param XMLProcess $xmlp XMLProcess instance
     */
    public function __construct(XMLProcess $xmlp)
    {
        $this->_xmlp = $xmlp;
    }

    /**
     * Builds the form
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array                $options Form options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'copyFields',
            'collection',
            array(
                'type'      => new CopyFieldForm($this->_xmlp),
                'allow_add' => true
            )
        );
    }

    /**
     * Sets default options
     *
     * @param OptionsResolverInterface $resolver Resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bach\AdministrationBundle\Entity' .
                '\Helpers\FormObjects\CopyFields',
            )
        );
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName()
    {
        return 'copyFieldsForm';
    }
}
