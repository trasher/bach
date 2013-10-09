<?php
/**
 * Document form
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Document form
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DocumentType extends AbstractType
{
    private $_files_types;

    /**
     * Main constructor
     *
     * @param array $files_types Known files types
     */
    public function __construct(array $files_types)
    {
        foreach ( $files_types as $ftype ) {
            $this->_files_types[$ftype] = strtoupper($ftype);
        }
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
        $builder
            ->add(
                'file',
                'file',
                array(
                    "label" => _("File to publish")
                )
            )
            ->add(
                'extension',
                'choice',
                array(
                    "choices" => $this->_files_types,
                    "label"    =>    _("File format")
                )
            )
            ->add(
                'perform',
                'submit',
                array(
                    'label' => _("Add to queue"),
                )
            )
            ->add(
                'performall',
                'submit',
                array(
                    'label' => _("Publish"),
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
                'data_class'    => 'Bach\IndexationBundle\Entity\Document'
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
        return 'document';
    }
}
