<?php
/**
 * Copy field form object
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

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;


/**
 * Copy field form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class CopyField
{
    public $source;
    public $dest;
    public $maxChars;

    /**
     * Constructor
     *
     * @param SolrXMLElement $fieldElt Solr field
     */
    public function __construct(SolrXMLElement $fieldElt = null)
    {
        if ($fieldElt != null) {
            $attr = $fieldElt->getAttribute('source');
            $this->source = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('dest');
            $this->dest = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('maxChars');
            $this->maxChars = $attr !== null ? $attr->getValue() : null;
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
        $solrXMLElt = new SolrXMLElement('copyField');
        $attr = new SolrXMLAttribute('source');
        $attr->setValue($this->source);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('dest');
        $attr->setValue($this->dest);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('maxChars');
        $attr->setValue($this->maxChars);
        $solrXMLElt->addAttribute($attr);
        $schema = $xmlP->getElementsByName('schema');
        $schema->addElement($solrXMLElt);
    }

    /**
     * Get Solr XML element, with relevant attributes
     *
     * @return SolrXMLElement
     */
    public function getSolrXMLElement()
    {
        $elt = new SolrXMLElement('copyField');
        $attr = new SolrXMLAttribute('source', $this->source);
        $elt->addAttribute($attr);
        $attr = new SolrXMLAttribute('dest', $this->dest);
        $elt->addAttribute($attr);
        if ($this->maxChars != null) {
            $attr = new SolrXMLAttribute('maxChars', $this->maxChars);
            $elt->addAttribute($attr);
        }
        return $elt;
    }
}
