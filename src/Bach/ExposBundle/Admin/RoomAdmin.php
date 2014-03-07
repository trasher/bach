<?php
/**
 * Bach expositions rooms adminstration (for SonataAdminBundle)
 *
 * PHP version 5
 *
 * @category Expos
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Bach\ExposBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Pix\SortableBehaviorBundle\Services\PositionHandler;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Bach expositions rooms management
 *
 * @category Expos
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class RoomAdmin extends Admin
{
    protected $datagridValues = array(
        '_page'         => 1,
        '_sort_order'   => 'ASC',
        '_sort_by'      => 'position'
    );

    public $last_position = 0;

    /**
     * Constructor
     *
     * @param string $code               ?
     * @param string $class              ?
     * @param string $baseControllerName ?
     */
    public function __construct($code, $class, $baseControllerName)
    {
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
                'exposition',
                'sonata_type_model',
                array(
                    'btn_add'   => false,
                    'empty_value' => _('Select an exposition')
                ),
                array(
                    'placeholder' => _('No exposition selected')
                )
            )->add(
                'name',
                null,
                array(
                    'label' => _('Name')
                )
            )->add(
                'url',
                null,
                array(
                    'required'  => false,
                    'label'     => _('URL')
                )
            )->add(
                'description',
                'ckeditor',
                array(
                    'config_name'   => 'bach_head_edit',
                    'label'         => _('Short description')
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
            ->add(
                'name',
                null,
                array(
                    'label' => _('Name')
                )
            );
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
                'name',
                null,
                array(
                    'label' => _('Name')
                )
            )->add(
                'exposition',
                null,
                array(
                    'label' => _('Exposition')
                )
            )->add(
                'url',
                null,
                array(
                    'label' => _('URL')
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

}
