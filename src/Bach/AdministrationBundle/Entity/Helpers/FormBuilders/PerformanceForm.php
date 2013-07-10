<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

class PerformanceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('queryResultWindowsSize', 'integer', array(
                'label' => 'Query result windows size'
                ));
        $builder->add('documentCacheClass', 'choice', array(
                'label'  => 'Document cache class',
                'choices' => array('solr.LRUCache' => 'solr.LRUCache', 'solr.FastLRUCache' => 'solr.FastLRUCache')
                ));
        $builder->add('documentCacheSize', 'integer', array(
                'label'    => 'Document cache size'
        ));
        $builder->add('documentCacheInitialSize', 'integer', array(
                'label'    => 'Document cache initial size'
        ));
        $builder->add('queryResultMaxDocsCached', 'integer', array(
                'label'    => 'Query result max docs cached'
        ));
        $builder->add('queryResultClassSize', 'choice', array(
                'label'    => 'Query result class size',
                'choices' => array('solr.LRUCache' => 'solr.LRUCache', 'solr.FastLRUCache' => 'solr.FastLRUCache')
        ));
        $builder->add('queryResultCacheSize', 'integer', array(
                'label'    => 'Query result cache size'
        ));
        $builder->add('queryResultInitialCacheSize', 'integer', array(
                'label'    => 'Query result initial cache size'
        ));
        $builder->add('queryResultAutowarmCount', 'integer', array(
                'label'    => 'Query result autowarm count'
        ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\Performance',
        ));
    }
    
    public function getName()
    {
        return 'performance';
    }
}