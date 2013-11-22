<?php
/**
 * Bach toponym
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Bach toponym
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Toponym
{
    const TYPE_TOWN = 0;
    const TYPE_SPECIFIC = 1;
    const TYPE_NOMINATED = 2;
    const TYPE_COUNTRY = 3;
    const TYPE_STATE = 4;

    private $_original;
    private $_type;
    private $_name;
    private $_specific_name;
    private $_country;
    private $_county;
    private $_nomination;
    private $_subdivision;
    private $_reject;

    /**
     * Default constructor
     *
     * @param string $name Optionnal name to parse
     */
    public function __construct($name = null)
    {
        $this->_type = self::TYPE_TOWN;

        if ( $name !== null) {
            $this->parse($name);
        }
    }

    /**
     * Parse a given toponym, according to NF Z44-081
     *
     * Entry string must match something like:
     * - Nîmes
     * - Nîmes (Gard, France)
     * - Rhône (France ; cours d'eau)
     * - Alsace (France ; région)
     * - Seine, La (France)
     * - Seine, La (France ; fleuve)
     * - ...
     *
     * Some forms shall not be recognized. Without any specific designation,
     * toponym will default to a town.
     *
     * @param string $name Toponym
     *
     * @return Toponym
     */
    public function parse($name)
    {
        $this->_original = $name;
        $regex = '#(.[^\(]+)\s?(\((.[^;]+)(\s?;\s?(.+))?\))?(\s--(.+))?#';

        //handle non standards rejected forms
        $rejects_regex = '#(.[^\(]+\s\((.[^\(]+)\))\s\(.+\)#';
        if ( preg_match($rejects_regex, $name, $rejects) ) {
            //print_r($rejects);
            $_good_name = preg_replace(
                '# \(.[^\(]+\)#',
                '',
                $rejects[1]
            );
            //on the example, Soyouz (ELS) (Sinnamary, Guyane, France ; ensemble de lancement),
            //rejected form is not relevant
            $this->_reject = $rejects[2];
            $name = str_replace(
                $rejects[1],
                $_good_name,
                $name
            );
        }

        if ( preg_match($regex, $name, $matches) ) {
            //print_r($matches);
            $this->_name = trim($matches[1]);

            if ( isset($matches[5]) && trim($matches[5] !== '') ) {
                $this->_nomination = trim($matches[5]);
                if ( $this->_nomination === 'département' ) {
                    $this->_type = self::TYPE_STATE;
                    $this->_name = trim($matches[1]);
                } else {
                    $this->_type = self::TYPE_NOMINATED;
                    $this->_specific_name = trim($matches[1]);
                    $this->_name = null;
                }
            }

            if ( isset($matches[7]) && trim($matches[7]) !== '' ) {
                $this->_subdivision = trim($matches[7]);
            }

            if ( isset($matches[3]) ) {
                $splitted = explode(',', $matches[3]);
                switch ( count($splitted) ){
                case 1:
                    //(France)
                    $this->_country = trim($splitted[0]);
                    break;
                case 2:
                    //(Gard, France)
                    $this->_county = trim($splitted[0]);
                    $this->_country = trim($splitted[1]);
                    break;
                case 3:
                    //(Le Thor, Vaucluse, France)
                    if ( $this->_type === self::TYPE_TOWN ) {
                        $this->_type = self::TYPE_SPECIFIC;
                    }
                    if ( $this->_specific_name === null ) {
                        $this->_specific_name = trim($matches[1]);
                    }
                    $this->_name = trim($splitted[0]);
                    $this->_county = trim($splitted[1]);
                    $this->_country = trim($splitted[2]);
                    break;
                default:
                    throw new \RuntimeException(
                        __CLASS__ . 'Found ' . count($splitted) .
                        ' (original: ' . $name
                    );
                }
            }
        }

        $this->_checkForCountry();

        return $this;
    }

    /**
     * Check if current name is a country
     *
     * @return void
     */
    private function _checkForCountry()
    {
        if ( $this->_name !== null
            && $this->_country === null
            && $this->_county === null
        ) {
            /** TODO: calculate locale */
            $countries = \Symfony\Component\Locale\Locale::getDisplayCountries('fr');

            if ( in_array($this->_name, $countries) ) {
                $this->_type = self::TYPE_COUNTRY;
                $this->_country = $this->_name;
            }
        }
    }

    /**
     * Get toponym type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get toponym name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get specific name
     *
     * @return string
     */
    public function getSpecificName()
    {
        return $this->_specific_name;
    }

    /**
     * Get toponym country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->_country;
    }

    /**
     * Get toponym county
     *
     * @return string
     */
    public function getCounty()
    {
        return $this->_county;
    }

    /**
     * Get toponym nomination
     *
     * @return string
     */
    public function getNomination()
    {
        return $this->_nomination;
    }

    /**
     * Get toponym subdivision
     *
     * @return string
     */
    public function getSubdivision()
    {
        return $this->_subdivision;
    }

    /**
     * Get original name
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->_original;
    }

    /**
     * Can current toponym be localized on OSM?
     *
     * @return boolean
     */
    public function canBeLocalized()
    {
        //per default, guess it shall be.
        $can = true;

        //check for some cases that prevent correct localization
        if ( $this->_subdivision !== null ) {
            $can = false;
        }

        if ( $this->_nomination !== null
            && $this->_nomination !== 'département'
            && $can === true
        ) {
            $can = false;
        }

        return $can;
    }

    /**
     * Get string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_original;
    }
}
