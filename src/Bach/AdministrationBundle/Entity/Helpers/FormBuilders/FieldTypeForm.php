<?php
/**
 * Field type form
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
use Symfony\Component\Form\AbstractType;
use Bach\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Field type form
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class FieldTypeForm extends AbstractType
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
        $bachTagType = BachSchemaConfigReader::FIELD_TYPE_TAG;
        $reader = new BachSchemaConfigReader();

        // Form attributes
        $attr = $reader->getAttributeByTag($bachTagType, 'name');
        $builder->add(
            'name',
            'text',
            array(
                'label'    => $attr->getLabel(),
                'required' => true
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'class');
        $builder->add(
            'class',
            'choice',
            array(
                'label'    => $attr->getLabel(),
                'required' => true,
                'choices'  => $this->_retrieveClassAttributeValues($reader)
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'sortMissingLast');
        $builder->add(
            'sortMissingLast',
            'checkbox',
            array(
                'label'    => $attr->getLabel(),
                'required' => false
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'sortMissingFirst');
        $builder->add(
            'sortMissingFirst',
            'checkbox',
            array(
                'label'    => $attr->getLabel(),
                'required' => false
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'positionIncrementGap');
        $builder->add(
            'positionIncrementGap',
            'integer',
            array(
                'label'    => $attr->getLabel(),
                'required' => false
            )
        );
        $attr = $reader->getAttributeByTag(
            $bachTagType,
            'autoGeneratePhraseQueries'
        );
        $builder->add(
            'autoGeneratePhraseQueries',
            'checkbox',
            array(
                'label'    => $attr->getLabel(),
                'required' => false
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
                '\Helpers\FormObjects\FieldType',
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
        return 'fieldTypeForm';
    }

    /**
     * Get available values for type attribute.
     *
     * @param BachSchemaConfigReader $reader Config reader
     *
     * @return multitype:NULL
     */
    private function _retrieveClassAttributeValues(BachSchemaConfigReader $reader)
    {
        $attr = $reader->getAttributeByTag(
            BachSchemaConfigReader::FIELD_TYPE_TAG,
            'class'
        );
        return $attr->getValues();
    }
}
