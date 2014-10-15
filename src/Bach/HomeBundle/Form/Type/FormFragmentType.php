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
 * @author   Sebastien Chaptal <sebastien.chaptal@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Bach\AdministrationBundle\Entity\SolrCore\Fields;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * fragment of advanced search form
 *
 * @category Search
 * @package  Bach
 * @author   Sebastien Chaptal  <sebastien.chaptal@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class FormFragmentType extends AbstractType
{
    private $_reader;
    private $_search_core;
    private $_data_class;

    /**
     * Constructor
     *
     * @param BachCoreAdminConfigReader $reader Config reader
     *
     */
    /*public function __construct( $reader= null, $search_core='', $data_class ) {
        $this->_reader = $reader;
        $this->_search_core = $search_core;
        $this->_data_class = $data_class;
    }*/

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
                'selectFields',
                'choice',
                array(
                    'choices'     => null,//$this->getFields(),
                    'required'    => false,
                    'label'       => 'Première option',
                    'label_attr'  => array (
                        'class'     => 'labelMatriculesForm'
                    ),
                    'empty_value' => 'Choisissez une option'
                )
            )
            ->add(
                'inputSearch',
                null,
                array(
                    'required'  => true,
                    'label'     => 'champ de recherche',
                    'label_attr'=> array (
                        'entrez votre requête'
                    ),
                    'attr'      => array(
                        'class' => 'inputMatriculesForm',
                    ),
                )
            );
    }


    /**
     *
     */
    /*public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bach\HomeBundle\Entity\formFragment'
            )
        );
    }*/

    /**
     *
     *
     */
    public function getName(){
        return 'formFragment';
    }
}
