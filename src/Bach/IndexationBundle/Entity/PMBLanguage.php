<?php
/**
 * Bach PMB Language entity
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
 * Bach PMB Language entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="pmb_language")
 *
 */
class PMBLanguage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uniqid;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="language")
     * @ORM\JoinColumn(name="pmbfile_id", referencedColumnName="uniqid")
     */
    protected $pmbfile;


    /**
     * Main constructor
     *
     * @param string        $type    Entity type
     * @param string        $content Entity content
     * @param PMBFileFormat $pmb     Entity pmb
     */
    public function __construct($type, $content,$pmb)
    {
        $this->pmbfile = $pmb;
        $this->content = self::convertCodeLanguage($content);
        $this->type = $type;
    }
    /**
     * Parse converter Language
     *
     * @param string $code code language
     *
     * @return string
     */
    public static function convertCodeLanguage($code)
    {
        $value=null;
        switch ($code) {
        case 'fre':
            $value = _('French');
            break;
        case 'Fre':
            $value = _('French');
            break;
        case 'ger':
            $value = _('German');
            break;
        case 'fro':
            $value = _('Old French');
            break;
        case 'eng':
            $value = _('English');
            break;
        case 'ame':
            $value = _('American');
            break;
        case 'bam':
            $value = _('Bambara');
            break;
        case 'dut':
            $value = _('Netherlandish');
            break;
        case 'rus':
            $value = _('Russian');
            break;
        case 'jpn':
            $value = _('Japan');
            break;
        default:
            $value=$code;
            break;
        }
        return $value;
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
     * Set type
     *
     * @param string $type type
     *
     * @return PMBLanguage
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set content
     *
     * @param string $content content
     *
     * @return PMBLanguage
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set languageassoc
     *
     * @param \Bach\IndexationBundle\Entity\PMBFileFormat $languageassoc languageassoc
     *
     * @return PMBLanguage
     */
    public function setLanguageassoc(\Bach\IndexationBundle\Entity\PMBFileFormat $languageassoc = null)
    {
        $this->languageassoc = $languageassoc;
        return $this;
    }

    /**
     * Get languageassoc
     *
     * @return \Bach\IndexationBundle\Entity\PMBFileFormat
     */
    public function getLanguage()
    {
        return $this->languageassoc;
    }
}
