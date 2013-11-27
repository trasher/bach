<?php
/**
 * Comment form
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Bach\HomeBundle\Entity\Comment;

/**
 * Comment form
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CommentType extends AbstractType
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
        $builder
            ->add(
                'subject',
                null,
                array(
                    "label" => _("Subject")
                )
            )
            ->add(
                'priority',
                'choice',
                array(
                    "choices" => array(
                        Comment::COMMENT        => _('Comment'),
                        Comment::IMPROVEMENT    => _('Improvement'),
                        Comment::BUG            => _('Bug')
                    ),
                    "label"    =>    _("Type")
                )
            )
            ->add(
                'message',
                'ckeditor',
                array(
                    'config_name'   => 'bach_comment_edit',
                    'label' => _('Message')
                )
            )
            ->add(
                'perform',
                'submit',
                array(
                    'label' => _("Send your comment"),
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
                'data_class'    => 'Bach\HomeBundle\Entity\Comment'
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
        return 'comment';
    }
}
