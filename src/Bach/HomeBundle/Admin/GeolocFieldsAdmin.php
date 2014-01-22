<?php
/**
 * Bach geolocalization fields adminstration (for SonataAdminBundle)
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Bach\HomeBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Bach\IndexationBundle\Entity\EADFileFormat;
use Bach\AdministrationBundle\Entity\SolrCore\Fields;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Bach geolocalization fields management
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class GeolocFieldsAdmin extends Admin
{
    private $_reader;
    private $_container;
    private $_search_core;
    private $_data_class;

    /**
     * Constructor
     *
     * @param string                    $code               ?
     * @param string                    $class              ?
     * @param string                    $baseControllerName ?
     * @param BachCoreAdminConfigReader $reader             Config reader.
     * @param string                    $search_core        Search core name
     * @param string                    $data_class         Data class name
     */
    public function __construct($code, $class, $baseControllerName, $reader,
        $search_core, $data_class
    ) {
        $this->_reader = $reader;
        $this->_search_core = $search_core;
        $this->_data_class = $data_class;
        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper Mapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'solr_fields_names',
                'choice',
                array(
                    'choices'   => $this->getFields(),
                    'label'     => _('Fields'),
                    'multiple'  => true
                )
            );
    }

    /**
     * Return fields list
     *
     * @return array
     */
    protected function getFields()
    {
        $fields = new Fields($this->_reader);
        $solr_fields = $fields->getFacetFields(
            $this->_search_core,
            $this->getExcludedFields()
        );
        return $solr_fields;
    }

    /**
     * Get excluded fields
     *
     * @return array
     */
    protected function getExcludedFields()
    {
        $class = $this->_data_class;
        return $class::$facet_excluded;
    }

    /**
     * Configure routes
     *
     * @param RouteCollection $collection Collection
     *
     * @return void
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('edit'));
    }

    /**
     * Container injenction
     *
     * @param ContainerInterface $container Container
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Retrieve localized label for field
     *
     * @param string $name Field name
     *
     * @return string
     */
    public function getFieldLabel($name)
    {
        $fields = new Fields();
        return $fields->getFieldLabel($name);
    }
}
