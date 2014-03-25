<?php
/**
 * Bach PMB Notice Link entity
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Bach PMB Notice Link entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="pmbnoticelink")
 *
 */
class PMBNoticeLink
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
    protected $type_notice_link;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $notice;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="notice")
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
     * Set type
     *
     * @param string $type type of notice
     *
     * @return PMBNoticeLink
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
     * Set notice
     *
     * @param string $notice link for notice
     *
     * @return PMBNoticeLink
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
        return $this;
    }

    /**
     * Get notice
     *
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * Set pmbfile
     *
     * @param \Bach\IndexationBundle\Entity\PMBFileFormat $pmbfile pmbfile
     *
     * @return PMBNoticeLink
     */
    public function setNoticeassoc(PMBFileFormat $pmbfile)
    {
        $this->pmbfile = $pmbfile;
        return $this;
    }

    /**
     * Get noticeassoc
     *
     * @return PMBFileFormat
     */
    public function getpmbfile()
    {
        return $this->pmbfile;
    }
}
