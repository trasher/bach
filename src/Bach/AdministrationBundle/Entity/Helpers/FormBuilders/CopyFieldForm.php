<?php
/**
 * Copy field form
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

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Bach\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Copy field form
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CopyFieldForm extends AbstractType
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
        $bachTagType = BachSchemaConfigReader::COPY_FIELD_TAG;
        $reader = new BachSchemaConfigReader();

        // Form attributes
        $attr = $reader->getAttributeByTag($bachTagType, 'source');
        $builder->add(
            'source',
            'choice',
            array(
                'label'    => $attr->getLabel(),
                'required' => true,
                'choices'  => $this->retreiveUniqueKeyValues()
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'dest');
        $builder->add(
            'dest',
            'choice',
            array(
                'label'    => $attr->getLabel(),
                'required' => true,
                'choices'  => $this->retreiveUniqueKeyValues()
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'maxChars');
        $builder->add(
            'maxChars',
            'integer',
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
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\CopyField',
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
        return 'copyFieldForm';
    }

    /**
     * Get available values for source and dest attributes. Only values 
     * from the schema.xml in field tags can be used.
     *
     * @return multitype:NULL
     */
    private function retreiveUniqueKeyValues()
    {
        $choices = array();
        $fieldsTag = $this->_xmlp->getElementsByName('fields');
        $fieldsTag= $fieldsTag[0];
        $fields = $fieldsTag->getElementsByName('field');
        foreach ($fields as $t) {
            $schemaAttr = $t->getAttribute('name');
            if (!in_array($schemaAttr->getValue(), $choices)) {
                $choices[$schemaAttr->getValue()] = $schemaAttr->getValue();
            }
        }
        $fields = $fieldsTag->getElementsByName('dynamicField');
        foreach ($fields as $t) {
            $schemaAttr = $t->getAttribute('name');
            if (!in_array($schemaAttr->getValue(), $choices)) {
                $choices[$schemaAttr->getValue()] = $schemaAttr->getValue();
            }
        }
        return $choices;
    }
}
