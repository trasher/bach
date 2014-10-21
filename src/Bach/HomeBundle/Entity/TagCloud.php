<?php
/**
 * Bach tag cloud management
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
use Doctrine\ORM\EntityManager;

/**
 * Bach tag cloud management
 *
 * @ORM\Entity
 * @ORM\Table(name="tag_cloud")
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class TagCloud
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
     * @var integer
     *
     * @ORM\Column(name="number", type="integer")
     */
    protected $number;

    /**
     * @var text
     *
     * @ORM\Column(name="solr_fields_names", type="text")
     */
    protected $solr_fields_names;

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
     * Set solr_fields_names
     *
     * @param array $fields Fields names
     *
     * @return TagCloud
     */
    public function setSolrFieldsNames($fields)
    {
        $this->solr_fields_names = serialize($fields);
        return $this;
    }

    /**
     * Get solr_fields_names
     *
     * @return string
     */
    public function getSolrFieldsNames()
    {
        return unserialize($this->solr_fields_names);
    }

    /**
     * Set number
     *
     * @param integer $number Number of occurences in cloud
     *
     * @return TagCloud
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Load cloud configuration
     *
     * @param EntityManager $em Entity manager
     *
     * @return TagCloud
     */
    public function loadCloud(EntityManager $em)
    {
        $qb = $em->createQueryBuilder()
            ->add('select', 't')
            ->add('from', 'Bach\HomeBundle\Entity\TagCloud t');

        $query = $qb->getQuery();
        $results = $query->getResult();

        if ( count($results) > 0 ) {
            return $results[0];
        } else {
            $this->setSolrFieldsNames(array());
            $this->setNumber(20);
            $em->persist($this);
            $em->flush();
            return $this;
        }
    }

    /**
     * String representation
     *
     * @return string
     */
    public function __toString()
    {
        return _('Tag cloud');
    }
}
