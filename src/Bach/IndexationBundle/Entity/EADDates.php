<?php

namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EADDates
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class EADDates
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
     * @ORM\Column(name="date", type="string", length=255)
     */
    protected $date;

    /**
     * @var string
     *
     * @ORM\Column(name="normal", type="string", length=255, nullable=true)
     */
    protected $normal;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    protected $label;

    /**
     * @var string
     *
     * @ORM\Column(name="calendar", type="string", length=255, nullable=true)
     */
    protected $calendar;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="begin", type="date", nullable=true)
     */
    protected $begin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="date", nullable=true)
     */
    protected $end;

    /**
     * @ORM\ManyToOne(targetEntity="EADFileFormat", inversedBy="dates")
     * @ORM\JoinColumn(name="eadfile_id", referencedColumnName="uniqid")
     */
    protected $eadfile;

    /**
      * The constructor
      *
      * @param EADFileFormat $ead  EAD document referenced
      * @param array         $data The input data
      */
    public function __construct($ead, $data)
    {
        $this->eadfile = $ead;
        $this->date = $data['value'];

        //set begin and end dates
        $this->_parseDates($data);

        foreach ( $data['attributes'] as $attr=>$value) {
            switch ( $attr ){
            case 'label':
            case 'calendar':
            case 'normal':
            case 'type':
                $this->$attr = $value;
                break;
            default:
                //FIXME: throw a warning, attribute is not mapped
            }
        }
    }

    /**
     * Parse date
     *
     * @param array $data Data
     *
     * @return void
     */
    private function _parseDates($data)
    {
        $date = $data['value'];
        $bdate = null;
        $edate = null;
        $same = true;

        if ( isset($data['attributes']['normal']) ) {
            $extrems = explode('/', $data['attributes']['normal']);
            $bdate = $extrems[0];
            if ( isset($extrems[1]) ) {
                $edate = $extrems[1];
                $same = false;
            } else {
                $edate = $bdate;
            }
        } else {
            //here the date is unique, and probably not well formed
            $bdate = $date;
            $edate = $date;
        }

        $now = new \DateTime();

        //regexp to check if date is Y-M-D, Y-M, Y (or the same without dashes)
        $reg = '/^(\d{4})-?(\d{2})?-?(\d{2})?$/';

        //try to set begin date
        try  {
            $this->begin = new \DateTime($bdate);
            if ( preg_match($reg, $bdate, $matches) ) {
                //DateTime initialized with year only will give current year...
                if ( count($matches) == 2 ) {
                    $this->begin = \DateTime::createFromFormat('Y', $bdate);
                }
                if ( count($matches) <= 3 ) {
                    //day is not provided. set to 1st.
                    $this->begin->modify('first day of this month');
                }
                if ( count($matches) == 2 ) {
                    //month is not provided. set to 1st.
                    $this->begin->modify('january');
                }
            }
            //if date is in the future, we remove it
            if ( $this->begin > $now ) {
                $this->begin = null;
            }
        } catch ( \Exception $e ) {
            //TODO: add a parameter somewhere to decide if we throw or not
            //throw $e;
        }

        //try to set end date
        try {
            $this->end = new \DateTime($edate);
            if ( preg_match($reg, $edate, $matches) ) {
                if ( count($matches) == 2 ) {
                    $this->end = \DateTime::createFromFormat('Y', $edate);
                }
                if ( count($matches) <= 3 ) {
                    //day is not provided. set to last.
                    $this->end->modify('last day of this month');
                }
                if ( count($matches) == 2 ) {
                    //month is not provided. set to 1st.
                    $this->end->modify('december');
                }
            }
            //if date is in the future, we remove it
            if ( $this->end > $now ) {
                $this->end = null;
            }
        } catch ( \Exception $e ) {
            //TODO: add a parameter somewhere to decide if we throw or not
            //throw $e;
        }
        
    }

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
     * Set date
     *
     * @param string $date Date (textual)
     *
     * @return EADDates
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set normal
     *
     * @param string $normal Normalized form
     *
     * @return EADDates
     */
    public function setNormal($normal)
    {
        $this->normal = $normal;
        return $this;
    }

    /**
     * Get normal
     *
     * @return string
     */
    public function getNormal()
    {
        return $this->normal;
    }

    /**
     * Set label
     *
     * @param string $label label
     *
     * @return EADDates
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set calendar
     *
     * @param string $calendar calendar
     *
     * @return EADDates
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $calendar;
        return $this;
    }

    /**
     * Get calendar
     *
     * @return string
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Set type
     *
     * @param string $type type
     *
     * @return EADDates
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
     * Set begin
     *
     * @param \DateTime $begin Begin date
     *
     * @return EADDates
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;
        return $this;
    }

    /**
     * Get begin
     *
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set end
     *
     * @param \DateTime $end End date
     *
     * @return EADDates
     */
    public function setEnd($end)
    {
        $this->end = $end;
        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set eadfile
     *
     * @param \Bach\IndexationBundle\Entity\UniversalFileFormat\EADFileFormat $eadfile
     * @return EADDates
     */
    public function setEadfile(\Bach\IndexationBundle\Entity\UniversalFileFormat\EADFileFormat $eadfile = null)
    {
        $this->eadfile = $eadfile;
    
        return $this;
    }

    /**
     * Get eadfile
     *
     * @return \Bach\IndexationBundle\Entity\UniversalFileFormat\EADFileFormat 
     */
    public function getEadfile()
    {
        return $this->eadfile;
    }
}
