<?php
/**
 * Mapper for EAD data
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity\Mapper;

use Bach\IndexationBundle\DriverMapperInterface;
use Bach\IndexationBundle\Entity\EADFileFormat;

/**
 * Mapper for EAD data
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class EADDriverMapper implements DriverMapperInterface
{
    private $_eadid;

    /**
     * Translate eadheader
     *
     * @param array $data EAD header data
     *
     * @return array
     */
    public function translateHeader($data)
    {
        $mapped_data = array();

         $header_elements = array(
            'headerId' => 'eadid',
            'headerTitle' => 'filedesc/titlestmt/titleproper',
            'headerAuthor' => 'filedesc/titlestmt/author',
            'headerDate'    => 'filedesc/publicationstmt/date',
            'headerPublisher'   => 'filedesc/publicationstmt/publisher',
            'headerAddress'     => 'filedesc/publicationstmt/address/addressline',
            'headerLanguage'    => 'profiledesc/langusage/language'
        );

        foreach ( $header_elements as $map=>$element ) {
            if ( array_key_exists($element, $data)
                && $map !== 'headerLanguage'
                && isset($data[$element][0])
            ) {
                $mapped_data[$map] = $data[$element][0]['value'];
            } else if ( array_key_exists($element, $data)
                && isset($data[$element][0])
                && $map === 'headerLanguage'
                && array_key_exists(
                    'langcode',
                    $data[$element][0]['attributes']
                )
            ) {
                $mapped_data[$map]
                    = $data[$element][0]['attributes']['langcode'];
            }
        }

        return $mapped_data;
    }

    /**
     * Translate archdesc
     *
     * @param array $data Archdesc data
     *
     * @return array
     */
    public function translateArchdesc($data)
    {
        $mapped_data = array();

        $archdesc_elements = array(
            'cUnitId'           => 'did/unitid',
            'cUnitTitle'        => 'did/unittitle',
            'cUnitDate'         => 'did/unitdate',
            'cScopeContent'     => 'scopecontent',
            'cControlacces'     => 'controlacces',
            'fragment'          => 'fragment'
        );

        foreach ( $archdesc_elements as $map=>$element ) {
            if ( array_key_exists($element, $data) ) {
                $mapped_data[$map] = $data['c'][$element][0]['value'];
            }
        }

        $mapped_data['fragmentid'] = $this->_eadid . $data['id'];

        return $mapped_data;
    }

    /**
     * Set eadid
     *
     * @param string $id eadid
     *
     * @return void
     */
    public function setEadId($id)
    {
        $this->_eadid = $id;
    }

    /**
     * Translate elements
     *
     * @param array $data Document data
     *
     * @return array
     */
    public function translate($data)
    {
        $mapped_data = array();

        $ead_elements = array(
            'cUnitid'       => 'did/unitid',
            'cUnittitle'    => 'did/unittitle',
            'cScopcontent'  => 'scopecontent',
            'cControlacces' => 'controlacces'
        );

        if ( array_key_exists('parents', $data['c']) ) {
            $mapped_data['parents'] = implode(
                '/',
                array_keys($data['c']['parents'])
            );
            $mapped_data['parents_titles'] = $data['c']['parents'];
        }

        $mapped_data['fragmentid'] = $this->_eadid . '_' . $data['id'];

        foreach ( $ead_elements as $map=>$element ) {
            if ( array_key_exists($element, $data['c'])
                && count($data['c'][$element])
                && $element !== 'parents'
                || array_key_exists($element, $data['c'])
                && $element === 'parents'
            ) {
                $mapped_data[$map] = $data['c'][$element][0]['value'];
            }
        }

        $ead_mulitple_elements = array(
            'cCorpname'    => './/corpname',
            'cFamname'     => './/famname',
            'cGenreform'   => './/genreform',
            'cGeogname'    => './/geogname',
            'cName'        => './/name',
            'cPersname'    => './/persname',
            'cSubject'     => './/subject',
            'cUnitDate'    => './/unitdate',
            'cDate'        => './/date',
            'fragment'     => 'fragment',
            'daolist'      => './/daoloc|.//dao|.//archref[not(contains(@href, \'http://\')) and contains(@href, \'.pdf\')]',
            'cTitle'       => './/title',
            'cFunction'     => './/function'
        );
        $descriptors = EADFileFormat::$descriptors;

        foreach ( $ead_mulitple_elements as $map=>$element ) {
            if ( array_key_exists($element, $data['c'])
                && count($data['c'][$element])
            ) {
                if (in_array($map, $descriptors) ) {
                    $mapped_data['descriptors'][$map] = $data['c'][$element];
                } else {
                    $mapped_data[$map] = $data['c'][$element];
                }
            }
        }

        //c elements order
        if ( isset($data['c']['order']) ) {
            $mapped_data['elt_order'] = $data['c']['order'];
        }

        return $mapped_data;
    }
}
