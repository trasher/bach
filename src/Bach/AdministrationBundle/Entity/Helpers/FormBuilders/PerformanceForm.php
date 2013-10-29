<?php
/**
 * Performance form
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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Performance form
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class PerformanceForm extends AbstractType
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
        $builder->add(
            'queryResultWindowsSize',
            'integer',
            array(
                'label' => 'Query result windows size'
            )
        );
        $builder->add(
            'documentCacheClass',
            'choice',
            array(
                'label'     => 'Document cache class',
                'choices'   => array(
                    'solr.LRUCache' => 'solr.LRUCache',
                    'solr.FastLRUCache' => 'solr.FastLRUCache'
                )
            )
        );
        $builder->add(
            'documentCacheSize',
            'integer',
            array(
                'label'     => 'Document cache size'
            )
        );
        $builder->add(
            'documentCacheInitialSize',
            'integer',
            array(
                'label'     => 'Document cache initial size'
            )
        );
        $builder->add(
            'queryResultMaxDocsCached',
            'integer',
            array(
                'label'     => 'Query result max docs cached'
            )
        );
        $builder->add(
            'queryResultClassSize',
            'choice',
            array(
                'label'     => 'Query result class size',
                'choices'   => array(
                    'solr.LRUCache' => 'solr.LRUCache',
                    'solr.FastLRUCache' => 'solr.FastLRUCache'
                )
            )
        );
        $builder->add(
            'queryResultCacheSize',
            'integer',
            array(
                'label'    => 'Query result cache size'
            )
        );
        $builder->add(
            'queryResultInitialCacheSize',
            'integer',
            array(
                'label'    => 'Query result initial cache size'
            )
        );
        $builder->add(
            'queryResultAutowarmCount',
            'integer',
            array(
                'label'    => 'Query result autowarm count'
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
                'data_class' => 'Bach\AdministrationBundle\Entity\Helpers\FormObjects\Performance',
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
        return 'performance';
    }
}
