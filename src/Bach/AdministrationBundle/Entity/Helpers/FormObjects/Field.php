<?php
/**
 * Field form object
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
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

/**
 * Field form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class Field
{
    public $name;
    public $type;
    public $indexed = null;
    public $stored = null;
    public $multiValued = null;
    public $default = null;
    public $required = null;

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
            $attr = $fieldElt->getAttribute('type');
            $this->type = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('indexed');
            if ( $attr !== null ) {
                $this->indexed = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('stored');
            if ( $attr !== null ) {
                $this->stored = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('multiValued');
            if ( $attr !== null ) {
                $this->multiValued = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('default');
            if ( $attr !== null ) {
                $this->default = $attr->getValue();
            }
            $attr = $fieldElt->getAttribute('required');
            if ( $attr !== null ) {
                $this->required = $this->_toBoolean($attr->getValue());
            }
        }
    }

    /**
     * Get Solr XML element, with relevant attributes
     *
     * @return SolrXMLElement
     */
    public function getSolrXMLElement()
    {
        $elt = new SolrXMLElement('field');
        $attr = new SolrXMLAttribute('name', $this->name);
        $elt->addAttribute($attr);
        $attr = new SolrXMLAttribute('type', $this->type);
        $elt->addAttribute($attr);
        if ($this->indexed != null) {
            $attr = new SolrXMLAttribute(
                'indexed',
                $this->indexed ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        if ($this->stored != null) {
            $attr = new SolrXMLAttribute(
                'stored',
                $this->stored ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        if ($this->multiValued != null) {
            $attr = new SolrXMLAttribute(
                'multiValued',
                $this->multiValued ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        if ($this->default != '') {
            $attr = new SolrXMLAttribute('default', $this->default);
            $elt->addAttribute($attr);
        }
        if ($this->required != null) {
            $attr = new SolrXMLAttribute(
                'required',
                $this->required ? 'true' : 'false'
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
