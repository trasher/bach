<?php
/**
 * Bach comments
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Sonata\UserBundle\Entity\User;
use Bach\IndexationBundle\Entity\EADFileFormat;

/**
 * Bach comments management
 *
 * @ORM\Entity
 * @ORM\Table(name="comments")
 * @ORM\HasLifecycleCallbacks()
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Comment
{
    //priorities
    const COMMENT = 0;
    const IMPROVEMENT = 1;
    const BUG = 2;

    //sates
    const MODERATED = 0;
    const PUBLISHED = 1;
    const REJECTED = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="date", nullable=true)
     */
    protected $creation_date;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="opened_by_id", referencedColumnName="id", nullable=true)
     */
    protected $opened_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="close_date", type="date", nullable=true)
     */
    protected $close_date;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="closed_by_id", referencedColumnName="id")
     */
    protected $closed_by;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    protected $subject;

    /**
     * @var text
     *
     * @ORM\Column(name="message", type="text")
     */
    protected $message;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer")
     */
    protected $priority;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer")
     */
    protected $state;

    /**
     * @ORM\ManyToOne(targetEntity="\Bach\IndexationBundle\Entity\EADFileFormat", inversedBy="comments")
     * @ORM\JoinColumn(name="eadfile_id", referencedColumnName="uniqid")
     */
    protected $eadfile;

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
     * Set creation_date
     *
     * @param \DateTime $creationDate Date of creation
     *
     * @return Comment
     */
    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;
        return $this;
    }

    /**
     * Get creation_date
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * Get creation_date
     *
     * @return \DateTime
     */
    public function getLocalizedCreationDate()
    {
        return $this->creation_date->format(_('Y-m-d'));
    }


    /**
     * Set close_date
     *
     * @param \DateTime $closeDate Date of close
     *
     * @return Comment
     */
    public function setCloseDate($closeDate)
    {
        $this->close_date = $closeDate;
        return $this;
    }

    /**
     * Get close_date
     *
     * @return \DateTime
     */
    public function getCloseDate()
    {
        return $this->close_date;
    }

    /**
     * Set subject
     *
     * @param string $subject Subject
     *
     * @return Comment
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message Comment message
     *
     * @return Comment
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set priority
     *
     * @param integer $priority Comment priority
     *
     * @return Comment
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set state
     *
     * @param integer $state Comment state
     *
     * @return Comment
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }


    /**
     * Set opened_by
     *
     * @param User $openedBy User who opened the comment
     *
     * @return Comment
     */
    public function setOpenedBy(User $openedBy = null)
    {
        $this->opened_by = $openedBy;
        return $this;
    }

    /**
     * Get opened_by
     *
     * @return User
     */
    public function getOpenedBy()
    {
        return $this->opened_by;
    }

    /**
     * Set closed_by
     *
     * @param User $closedBy User who closed the comment
     *
     * @return Comment
     */
    public function setClosedBy(User $closedBy = null)
    {
        $this->closed_by = $closedBy;
        return $this;
    }

    /**
     * Get closed_by
     *
     * @return User
     */
    public function getClosedBy()
    {
        return $this->closed_by;
    }

    /**
     * Set eadfile
     *
     * @param EADFileFormat $eadfile Related EAD file
     *
     * @return Comment
     */
    public function setEadfile(EADFileFormat $eadfile = null)
    {
        $this->eadfile = $eadfile;
        return $this;
    }

    /**
     * Get eadfile
     *
     * @return EADFileFormat
     */
    public function getEadfile()
    {
        return $this->eadfile;
    }

    /**
     * Set creation date
     *
     * @ORM\PrePersist
     *
     * @return Comment
     */
    public function setCreationDateValue()
    {
        $this->creation_date = new \DateTime();
        return $this;
    }

    /**
     * Set default state
     *
     * @ORM\PrePersist
     *
     * @return Comment
     */
    public function setStateValue()
    {
        $this->state = self::MODERATED;
        return $this;
    }

    /**
     * string representation
     *
     * @return string
     */
    public function __tostring()
    {
        if ( $this->getSubject() ) {
            $txt = $this->getSubject();
            if ( $this->getOpenedBy() ) {
                $txt .= ' (by ' . $this->getOpenedBy()  . ')';
            }
            return $txt;
        } else {
            return _('New comment');
        }
    }

    /**
     * Get a list of known priorities
     *
     * @return array
     */
    public static function getKnownPriorities()
    {
        return array(
            self::COMMENT       => _('Comment'),
            self::IMPROVEMENT   => _('Improvement'),
            self::BUG           => _('Bug')
        );
    }

    /**
     * Get a list of known states
     *
     * @return array
     */
    public static function getKnownStates()
    {
        return array(
            self::MODERATED => _('Moderated'),
            self::PUBLISHED => _('Published'),
            self::REJECTED  => _('Rejected')
        );
    }

}