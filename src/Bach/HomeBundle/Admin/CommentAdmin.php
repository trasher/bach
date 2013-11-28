<?php
/**
 * Bach comments adminstration (for SonataAdminBundle)
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
use Bach\HomeBundle\Entity\Comment;

/**
 * Bach comments management
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CommentAdmin extends Admin
{
    protected $baseRouteName = 'admin_bach_homebundle_commentadmin';
    protected $baseRoutePattern = 'comment';

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
                'priority',
                'choice',
                array(
                    'choices'   => Comment::getKnownPriorities(),
                    'label'     => _('Type')
                )
            )
            ->add(
                'subject',
                null,
                array(
                    'label' => _('Subject')
                )
            )->add(
                'message',
                null,
                array(
                    'label' => _('Message')
                )
            )->add(
                'creation_date',
                null,
                array(
                    'label' => _('Creation date')
                )
            )->add(
                'opened_by',
                null,
                array(
                    'label' => _('From')
                )
            )->add(
                'message',
                null,
                array(
                    'label' => _('Message')
                )
            )->add(
                'state',
                'choice',
                array(
                    'choices'   => Comment::getKnownStates(),
                    'label'     => _('State')
                )
            );
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
                'subject',
                null,
                array(
                    'label' => _('Suject')
                )
            )->add(
                'opened_by',
                null,
                array(
                    'label' => _('From')
                )
            )->add(
                'state',
                null,
                array(
                    'label'         => _('State'),
                    'field_type'    => 'choice',
                    'field_options' => array(
                        'choices' => Comment::getKnownStates()
                    )
                )
            )->add(
                'priority',
                null,
                array(
                    'label'         => _('Type'),
                    'field_type'    => 'choice',
                    'field_options' => array(
                        'choices' => Comment::getKnownPriorities()
                    )
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
        $listMapper
            ->add(
                'opened_by',
                null,
                array(
                    'label' => _('From')
                )
            )->add(
                'priority',
                null,
                array(
                    'template'  => 'BachHomeBundle:Admin:show_type.html.twig',
                    'label'     => _('Type')
                )
            )->addIdentifier(
                'subject',
                null,
                array(
                    'label' => _('Subject')
                )
            )->add(
                'creation_date',
                null,
                array(
                    'label' => _('Date')
                )
            )->add(
                'state',
                null,
                array(
                    'template'  => 'BachHomeBundle:Admin:show_state.html.twig',
                    'label'     => _('State')
                )
            );
    }

    /**
     * Configure batch actions
     *
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if ( $this->hasRoute('edit') && $this->isGranted('edit') ) {
            $actions['publish'] = array(
                'label'             => _('Publish comments'),
                'ask_confirmation'  => true
            );
        }

        return $actions;
    }

    /**
     * Retrieve localized label for priority
     *
     * @param integer $index Priority
     *
     * @return string
     */
    public function getTypeLabel($index)
    {
        $types = Comment::getKnownPriorities();
        return $types[$index];
    }

    /**
     * Retrieve localized label for state
     *
     * @param integer $index State
     *
     * @return string
     */
    public function getStateLabel($index)
    {
        $states = Comment::getKnownStates();
        return $states[$index];
    }

}
