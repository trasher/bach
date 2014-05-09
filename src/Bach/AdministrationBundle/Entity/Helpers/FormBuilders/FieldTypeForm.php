<?php
/**
 * Field type form
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
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\Helpers\FormBuilders;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Bach\AdministrationBundle\Entity\SolrSchema\BachSchemaConfigReader;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Field type form
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class FieldTypeForm extends AbstractType
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
        $bachTagType = BachSchemaConfigReader::FIELD_TYPE_TAG;
        $reader = new BachSchemaConfigReader();

        // Form attributes
        $attr = $reader->getAttributeByTag($bachTagType, 'name');
        $builder->add(
            'name',
            'text',
            array(
                'label'    => $attr->getLabel(),
                'required' => true
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'class');
        $builder->add(
            'class',
            'choice',
            array(
                'label'    => $attr->getLabel(),
                'required' => true,
                'choices'  => $this->_retrieveClassAttributeValues($reader)
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'sortMissingLast');
        $builder->add(
            'sortMissingLast',
            'checkbox',
            array(
                'label'    => $attr->getLabel(),
                'required' => false
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'sortMissingFirst');
        $builder->add(
            'sortMissingFirst',
            'checkbox',
            array(
                'label'    => $attr->getLabel(),
                'required' => false
            )
        );
        $attr = $reader->getAttributeByTag($bachTagType, 'positionIncrementGap');
        $builder->add(
            'positionIncrementGap',
            'integer',
            array(
                'label'    => $attr->getLabel(),
                'required' => false
            )
        );
        $attr = $reader->getAttributeByTag(
            $bachTagType,
            'autoGeneratePhraseQueries'
        );
        $builder->add(
            'autoGeneratePhraseQueries',
            'checkbox',
            array(
                'label'    => $attr->getLabel(),
                'required' => false
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
                'data_class' => 'Bach\AdministrationBundle\Entity' .
                '\Helpers\FormObjects\FieldType',
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
        return 'fieldTypeForm';
    }

    /**
     * Get available values for type attribute.
     *
     * @param BachSchemaConfigReader $reader Config reader
     *
     * @return multitype:NULL
     */
    private function _retrieveClassAttributeValues(BachSchemaConfigReader $reader)
    {
        $attr = $reader->getAttributeByTag(
            BachSchemaConfigReader::FIELD_TYPE_TAG,
            'class'
        );
        return $attr->getValues();
    }
}
