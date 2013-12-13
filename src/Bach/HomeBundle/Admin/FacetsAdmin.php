<?php
/**
 * Bach facets adminstration (for SonataAdminBundle)
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
use Pix\SortableBehaviorBundle\Services\PositionHandler;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Bach facets management
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class FacetsAdmin extends Admin
{
    private $_reader;
    private $_container;
    private $_positionService;
    private $_search_core;

    protected $datagridValues = array(
        '_page'         => 1,
        '_sort_order'   => 'ASC',
        '_sort_by'      => 'position'
    );

    public $last_position = 0;

    /**
     * Constructor
     *
     * @param string                    $code               ?
     * @param string                    $class              ?
     * @param string                    $baseControllerName ?
     * @param BachCoreAdminConfigReader $reader             Config reader.
     * @param string                    $search_core        Search core name
     */
    public function __construct($code, $class, $baseControllerName, $reader,
        $search_core
    ) {
        $this->_reader = $reader;
        $this->_search_core = $search_core;
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
        //retrieve already used solr fields to exclude them
        $qb = $this->modelManager
            ->getEntityManager('Bach\HomeBundle\Entity\facets')
            ->createQueryBuilder()
            ->add('select', 'f.solr_field_name')
            ->add('from', 'Bach\HomeBundle\Entity\Facets f');

        if ( $this->getSubject()->getId() !== null ) {
            $qb
                ->add('where', 'f.solr_field_name != :curfield')
                ->setParameter('curfield', $this->getSubject()->getSolrFieldName());
        }

        $query = $qb->getQuery();
        $results = $query->getResult();
        $used = array_map(
            function ($value) {
                return $value['solr_field_name'];
            },
            $results
        );

        $fields = new Fields($this->_reader);
        $facet_fields = $fields->getFacetFields(
            $this->_search_core,
            array_merge(
                EADFileFormat::$facet_excluded,
                $used
            )
        );

        $formMapper
            ->add(
                'solr_field_name',
                'choice',
                array(
                    'choices' => $facet_fields,
                    'label' => _('Solr field name')
                )
            )->add(
                'active',
                null,
                array(
                    'required'  => false,
                    'label'     => _('Activate facet')
                )
            )->add(
                'fr_label',
                null,
                array(
                    'label' => _('French text')
                )
            )->add(
                'en_label',
                null,
                array(
                    'label' => _('English text')
                )
            );
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
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper Grid mapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('solr_field_name')
            ->add('active')
            ->add('fr_label')
            ->add('en_label');
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper List mapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $this->last_position = $this->positionService->getLastPosition(
            $this->getRoot()->getClass()
        );

        $listMapper
            ->addIdentifier(
                'solr_field_name',
                null,
                array(
                    'template'  => 'AdministrationBundle:Admin:show_field.html.twig',
                    'label'     => _('Solr field name')
                )
            )->add(
                'active',
                null,
                array(
                    'editable'  => true,
                    'label'     => _('Active')
                )
            )->add(
                'fr_label',
                null,
                array(
                    'label' => _('French text')
                )
            )->add(
                'en_label',
                null,
                array(
                    'label' => _('English text')
                )
            )->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'move' => array(
                            'template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig'
                        ),
                    )
                )
            );
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
     * Position handler injection
     *
     * @param PositionHandler $positionHandler Position handler
     *
     * @return void
     */
    public function setPositionService(PositionHandler $positionHandler)
    {
        $this->positionService = $positionHandler;
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
