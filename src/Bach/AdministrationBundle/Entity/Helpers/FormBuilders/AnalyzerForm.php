<?php
/**
 * Analyser form
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
use Bach\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Analyser form
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class AnalyzerForm extends AbstractType
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
        $bachTagType = BachSchemaConfigReader::ANALYZER_TAG;
        $reader = new BachSchemaConfigReader();

        // Form attributes
        $builder->add(
            'name',
            'text',
            array(
                'label'     => 'Field type name',
                'read_only' => true
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'class');
        $builder->add(
            'class',
            'choice',
            array(
                'label'         => $attr->getLabel(),
                'empty_value'   => _('(none)'),
                'required'      => false,
                'choices'       => $this->_retrieveClassAttributeValues($reader)
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
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\Analyzer',
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
        return 'analyzerTypeForm';
    }

    private function _retrieveClassAttributeValues(BachSchemaConfigReader $reader)
    {
        $attr = $reader->getAttributeByTag(
            BachSchemaConfigReader::ANALYZER_TAG,
            'class'
        );
        return $attr->getValues();
    }
}
