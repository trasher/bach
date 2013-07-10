<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormBuilders;

use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

use Anph\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

class UniqueKeyForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bachTagType = BachSchemaConfigReader::UNIQUE_KEY_TAG;
        $reader = new BachSchemaConfigReader();
    
        // Form attributes
        $attr = $reader->getAttributeByTag($bachTagType, 'uniqueKey');
        $builder->add('uniqueKey', 'choice', array(
                'label'    => $attr->getLabel(),
                'choices'  => $this->retreiveUniqueKeyValues()
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Anph\AdministrationBundle\Entity\Helpers\FormObjects\UniqueKey',
        ));
    }
    
    public function getName()
    {
        return 'uniqueKey';
    }
    
    /**
     * Get available values for unique key. Only values from the schema.xml in field tags
     * can be used.
     * @return multitype:NULL
     */
    private function retreiveUniqueKeyValues()
    {
        $choices = array();
        /*$session = new Session();
         if ($session->has('schema')) {
        $xmlProcess = $session->get('schema');*/
        $xmlP = new XMLProcess($_SESSION['_sf2_attributes']['coreName']);
        $types = $xmlP->getElementsByName('fields');
        $types = $types[0];
        $types = $types->getElementsByName('field');
        foreach ($types as $t) {
            $schemaAttr = $t->getAttribute('name');
            if (!$this->isContains($choices, $schemaAttr->getValue())) {
                $choices[$schemaAttr->getValue()] = $schemaAttr->getValue();
            }
        }
        //}
        return $choices;
    }
    
    private function isContains($choices, $name)
    {
        foreach ($choices as $c) {
            if ($c == $name) {
                return true;
            }
        }
        return false;
    }
}
