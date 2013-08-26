<?php
/**
 * Bach search form type
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Bach search form type
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SearchQueryFormType extends AbstractType
{
    private $_value = "";

    /**
     * Instanciate search form
     *
     * @param string $value Search term (default to empty string)
     */
    public function __construct($value = '')
    {
        $this->_value = $value;
    }

    /**
     * Builds form
     *
     * @param FormBuilderInterface $builder Builder
     * @param array                $options Options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'query',
            'text',
            array(
                'attr'  => array(
                    'placeholder'   => _('Enter your search'),
                     'class'        => 'largeinput',
                     'value'        => $this->_value,
                     'autocomplete' => 'off'
                )
            )
        )->add(
            'perform_search',
            'submit',
            array(
                'label' => _('Search'),
                'attr'  => array(
                    'class' => 'btn btn-primary'
                )
            )
        );
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'searchQuery';
    }
}
