<?php
/**
 * Unique key form
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

use Bach\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Unique key form
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class UniqueKeyForm extends AbstractType
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
        $bachTagType = BachSchemaConfigReader::UNIQUE_KEY_TAG;
        $reader = new BachSchemaConfigReader();

        // Form attributes
        $attr = $reader->getAttributeByTag($bachTagType, 'uniqueKey');
        $builder->add(
            'uniqueKey',
            'choice',
            array(
                'label'    => $attr->getLabel(),
                'choices'  => $this->_retrieveUniqueKeyValues()
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
                '\Helpers\FormObjects\UniqueKey',
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
        return 'uniqueKey';
    }

    /**
     * Get available values for unique key. Only values from 
     * the schema.xml in field tags can be used.
     *
     * @return multitype:NULL
     */
    private function _retrieveUniqueKeyValues()
    {
        $choices = array();
        $types = $this->_xmlp->getElementsByName('fields');
        $types = $types[0];
        $types = $types->getElementsByName('field');
        foreach ($types as $t) {
            $schemaAttr = $t->getAttribute('name');
            if (!in_array($schemaAttr->getValue(), $choices)) {
                $choices[$schemaAttr->getValue()] = $schemaAttr->getValue();
            }
        }
        return $choices;
    }
}
