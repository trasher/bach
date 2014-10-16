<?php
/**
 * Matricules search form
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Bach\AdministrationBundle\Entity\SolrCore\Fields;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Matricules search form
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class MatriculesType extends AbstractType
{
    private $_reader;
    private $_search_core;
    private $_data_class;
    protected $formsFragment;

    /**
     * Constructor
     *
     * @param FileFormat                $data_class  FileFormat
     * @param Corename                  $search_core Core name
     * @param BachCoreAdminConfigReader $reader      Config reader
     */
    public function __construct( $data_class, $search_core='', $reader=null )
    {
        $this->_reader = $reader;
        $this->_search_core = $search_core;
        $this->_data_class = $data_class;
        $this->formsFragment = new ArrayCollection();
    }

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
                'fragments',
                'collection',
                array(
                    'type' => new FormFragmentType(),
                    'data' => array (
                        array('label' => 'toto'),
                        array('sequence' => 2, 'title' => 'Bar'),
                    )
                )
            )->add(
                'perform',
                'submit',
                array(
                    'label' => _("Search"),
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

    /**
     * Set the class value for the matricule form
     *
     * @param OptionsResolverInterface $resolver resolver instance
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array (
                'data-class' => 'Bach\HomeBundle\Entity\FormMatricules',
            )
        );
    }

    /**
     * Get form fragments
     *
     * @return ArrayCollection
     */
    public function getFormsFragment()
    {
        return $this->formsFragment;
    }

    /**
     * Set the ArrayCollection formsFragment
     *
     * @param ArrayCollection $formsFragment the new collection
     *
     * @return ArrayCollection
     */
    public function setFormsFragment(ArrayCollection $formsFragment)
    {
        $this->formsFragment = $formsFragment;
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName()
    {
        return 'adv_matricules';
    }
}
