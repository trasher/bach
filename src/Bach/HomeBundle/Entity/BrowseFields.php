<?php
/**
 * Bach browsing fields
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

namespace Bach\HomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Bach browsing fields management
 *
 * @ORM\Entity
 * @ORM\Table(name="browse_fields")
 * @ORM\HasLifecycleCallbacks()
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class BrowseFields
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="solr_field_name", type="string", length=100)
     */
    protected $solr_field_name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @var string
     *
     * @ORM\Column(name="fr_label", type="string", length=255)
     */
    protected $fr_label;

    /**
     * @var string
     *
     * @ORM\Column(name="en_label", type="string", length=255)
     */
    protected $en_label;

    /**
     * @var int
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set solr_field_name
     *
     * @param string $solrFieldName Field name
     *
     * @return BrowseFields
     */
    public function setSolrFieldName($solrFieldName)
    {
        $this->solr_field_name = $solrFieldName;
        return $this;
    }

    /**
     * Get solr_field_name
     *
     * @return string
     */
    public function getSolrFieldName()
    {
        return $this->solr_field_name;
    }

    /**
     * Set active
     *
     * @param boolean $active Active
     *
     * @return BrowseFields
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set fr_label
     *
     * @param string $frLabel French label
     *
     * @return BrowseFields
     */
    public function setFrLabel($frLabel)
    {
        $this->fr_label = $frLabel;
        return $this;
    }

    /**
     * Get fr_label
     *
     * @return string
     */
    public function getFrLabel()
    {
        return $this->fr_label;
    }

    /**
     * Set en_label
     *
     * @param string $enLabel English label
     *
     * @return BrowseFields
     */
    public function setEnLabel($enLabel)
    {
        $this->en_label = $enLabel;
        return $this;
    }

    /**
     * Get en_label
     *
     * @return string
     */
    public function getEnLabel()
    {
        return $this->en_label;
    }

    /**
     * Retrieve localized facet label
     *
     * @param string $lang Language code
     *
     * @return string
     */
    public function getLabel($lang)
    {
        switch ($lang) {
        case 'fr':
        case 'fr_FR':
            return $this->getFrLabel();
            break;
        case 'en':
        case 'en_US':
        default:
            return $this->getEnLabel();
            break;
        }
    }

    /**
     * Set position
     *
     * @param int $position Facet position
     *
     * @return BrowseFields
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * String representation
     *
     * @return string
     */
    public function __toString()
    {
        if ( $this->getSolrFieldName() ) {
            return $this->getSolrFieldName();
        } else {
            return _('New browsing field');
        }
    }
}

