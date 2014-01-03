<?php
/**
 * Matricules search form
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Matricules search form
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class MatriculesType extends AbstractType
{

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
        $builder
            ->add(
                'nom',
                null,
                array(
                    "label"     => _("Name"),
                    'required'  => false
                )
            )
            ->add(
                'prenoms',
                null,
                array(
                    "label"     => _("Surname"),
                    'required'  => false
                )
            )
            ->add(
                'perform',
                'submit',
                array(
                    'label' => _("Search"),
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
        return 'comment';
    }
}
