<?php
/**
 * Bach PMB Title entity
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
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Bach PMB Title entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBTitle")
 *
 */
class PMBTitle
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uniqid;

    /**
     * @ORM\Column(type="string", length=100)

     */
    protected $title_section_part;

    /**
     * @ORM\Column(type="date", nullable=true, length=255)
     */
     protected $date_publication;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $sous_vedette_forme;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $langue;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $version;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $mention_arrangement;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $comment;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="title")
     * @ORM\JoinColumn(name="pmbfile_id", referencedColumnName="uniqid")
     */
    protected $pmbfile;
    /**
     * Main constructor
     *
     * @param array $data Entity data
     */
    public function __construct($data)
    {

    }
    /**
     * Get uniqid
     *
     * @return integer
     */
    public function getUniqid()
    {
        return $this->uniqid;
    }

    /**
     * Set title_section_part
     *
     * @param string $titleSectionPart titleSectionPart
     *
     * @return PMBTitle
     */
    public function setTitleSectionPart($titleSectionPart)
    {
        $this->title_section_part = $titleSectionPart;
        return $this;
    }

    /**
     * Get title_section_part
     *
     * @return string
     */
    public function getTitleSectionPart()
    {
        return $this->title_section_part;
    }

    /**
     * Set date_publication
     *
     * @param \DateTime $datePublication datePublication
     *
     * @return PMBTitle
     */
    public function setDatePublication($datePublication)
    {
        $this->date_publication = $datePublication;
        return $this;
    }

    /**
     * Get date_publication
     *
     * @return \DateTime
     */
    public function getDatePublication()
    {
        return $this->date_publication;
    }

    /**
     * Set sous_vedette_forme
     *
     * @param string $sousVedetteForme sousVedetteForme
     *
     * @return PMBTitle
     */
    public function setSousVedetteForme($sousVedetteForme)
    {
        $this->sous_vedette_forme = $sousVedetteForme;
        return $this;
    }

    /**
     * Get sous_vedette_forme
     *
     * @return string
     */
    public function getSousVedetteForme()
    {
        return $this->sous_vedette_forme;
    }

    /**
     * Set langue
     *
     * @param string $langue langue
     *
     * @return PMBTitle
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;
        return $this;
    }

    /**
     * Get langue
     *
     * @return string
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set version
     *
     * @param string $version version
     *
     * @return PMBTitle
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set mention_arrangement
     *
     * @param string $mentionArrangement mentionArrangement
     *
     * @return PMBTitle
     */
    public function setMentionArrangement($mentionArrangement)
    {
        $this->mention_arrangement = $mentionArrangement;

        return $this;
    }

    /**
     * Get mention_arrangement
     *
     * @return string
     */
    public function getMentionArrangement()
    {
        return $this->mention_arrangement;
    }

    /**
     * Set comment
     *
     * @param string $comment comment
     *
     * @return PMBTitle
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set titleassoc
     *
     * @param \Bach\IndexationBundle\Entity\PMBFileFormat $pmbfile pmbfile assoc
     *
     * @return PMBTitle
     */
    public function setTitleassoc(PMBFileFormat $pmbfile)
    {
        $this->titleassoc = $titleassoc;
    }

    /**
     * Get pmbfile
     *
     * @return \Bach\IndexationBundle\Entity\PMBFileFormat
     */
    public function getTitleassoc()
    {
        return $this->titleassoc;
    }
}
