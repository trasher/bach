<?php
/**
 * Field type form object
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

namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

/**
 * Field type form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

class FieldType
{
    public $name;
    public $class;
    public $sortMissingLast = null;
    public $sortMissingFirst = null;
    public $positionIncrementGap = null;
    public $autoGeneratePhraseQueries = null;

    /* Attributes that may be added to the application in the future. */
    /*public $indexed;
    public $stored;
    public $multiValued;
    public $omitNorms;
    public $omitTermFreqAndPositions;
    public $omitPositions;*/

    /**
     * Constructor
     *
     * @param SolrXMLElement $fieldElt Solr field
     */
    public function __construct(SolrXMLElement $fieldElt = null)
    {
        if ($fieldElt != null) {
            $attr = $fieldElt->getAttribute('name');
            $this->name = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('class');
            $this->class = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('sortMissingLast');
            if ( $attr !== null ) {
                $this->sortMissingLast = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('sortMissingFirst');
            if ( $attr !== null ) {
                $this->sortMissingFirst = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('positionIncrementGap');
            if ( $attr !== null ) {
                $this->positionIncrementGap = $attr->getValue();
            }
            $attr = $fieldElt->getAttribute('autoGeneratePhraseQueries');
            if ( $attr !== null ) {
                $this->autoGeneratePhraseQueries = $this->_toBoolean(
                    $attr->getValue()
                );
            }
        }
    }

    /**
     * Add field
     *
     * @param XMLProcess $xmlP XMLProcess instance
     *
     * @return void
     */
    public function addField(XMLProcess $xmlP)
    {
        $solrXMLElt = new SolrXMLElement('dynamicField');
        $attr = new SolrXMLAttribute('name');
        $attr->setValue($this->name);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('class');
        $attr->setValue($this->class);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('sortMissingLast');
        $attr->setValue($this->sortMissingLast);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('sortMissingFirst');
        $attr->setValue($this->sortMissingFirst);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('positionIncrementGap');
        $attr->setValue($this->positionIncrementGap);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('autoGeneratePhraseQueries');
        $attr->setValue($this->autoGeneratePhraseQueries);
        $solrXMLElt->addAttribute($attr);
        $fields = $xmlP->getElementsByName('types');
        $fields->addElement($solrXMLElt);
    }

    /**
     * Get Solr XML element, with relevant attributes
     *
     * @return SolrXMLElement
     */
    public function getSolrXMLElement()
    {
        $elt = new SolrXMLElement('fieldType');
        $attr = new SolrXMLAttribute('name', $this->name);
        $elt->addAttribute($attr);
        $attr = new SolrXMLAttribute('class', $this->class);
        $elt->addAttribute($attr);
        if ($this->sortMissingLast != null) {
            $attr = new SolrXMLAttribute(
                'sortMissingLast',
                $this->sortMissingLast ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        if ($this->sortMissingFirst != null) {
            $attr = new SolrXMLAttribute(
                'sortMissingFirst',
                $this->sortMissingFirst ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        $attr = new SolrXMLAttribute(
            'positionIncrementGap',
            $this->positionIncrementGap
        );
        $elt->addAttribute($attr);
        if ($this->autoGeneratePhraseQueries != null) {
            $attr = new SolrXMLAttribute(
                'autoGeneratePhraseQueries',
                $this->autoGeneratePhraseQueries ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        return $elt;
    }

    /**
     * Converts text to boolean...
     *
     * @param string $value Text value
     *
     * @return boolean
     */
    private function _toBoolean($value)
    {
        return $value == 'true' ? true : false;
    }
}
