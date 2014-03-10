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
    /**
     * Translate elements
     *
     * @param arrya $data Document data
     *
     * @return array
     */
    public function translate($data)
    {
        $mappedData = array();

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
            if ( array_key_exists($element, $data['header'])
                && $map !== 'headerLanguage'
                && isset($data['header'][$element][0])
            ) {
                $mappedData[$map] = $data['header'][$element][0]['value'];
            } else if ( array_key_exists($element, $data['header'])
                && isset($data['header'][$element][0])
                && $map === 'headerLanguage'
                && array_key_exists(
                    'langcode',
                    $data['header'][$element][0]['attributes']
                )
            ) {
                $mappedData[$map]
                    = $data['header'][$element][0]['attributes']['langcode'];
            }
        }
        $mappedData["headerSubtitle"] = null;

        $archdesc_elements = array(
            'archDescUnitId'            => 'did/unitid',
            'archDescUnitTitle'         => 'did/unittitle',
            'archDescUnitDate'          => 'did/unitdate',
            'archDescRepository'        => 'did/repository',
            'archDescLangMaterial'      => 'did/langmaterial',
            'archDescOrigination'       => 'did/origination',
            'archDescAcqInfo'           => 'acqinfo',
            'archDescScopeContent'      => 'scopecontent',
            'archDescArrangement'       => 'arrangement',
            'archDescAccessRestrict'    => 'accessrestrict'
        );

        // Partie spécifique à l'ead
        foreach ( $archdesc_elements as $map=>$element ) {
            if ( array_key_exists($element, $data['archdesc']) ) {
                $mappedData[$map] = $data['archdesc'][$element][0]['value'];
            }
        }

        $ead_elements = array(
            'cUnitid'       => 'did/unitid',
            'cUnittitle'    => 'did/unittitle',
            'cScopcontent'  => 'scopecontent',
            'cControlacces' => 'controlacces'
        );

        // Partie spécifique à l'ead
        if ( array_key_exists("parents", $data["c"]) ) {
            $mappedData["parents"] = implode("/", array_keys($data["c"]["parents"]));
            $mappedData["parents_titles"] = $data["c"]["parents"];
        }

        $mappedData['fragmentid'] = $mappedData['headerId'] . '_' . $data['id'];

        foreach ( $ead_elements as $map=>$element ) {
            if ( array_key_exists($element, $data['c'])
                && count($data['c'][$element])
                && $element !== 'parents'
                || array_key_exists($element, $data['c'])
                && $element === 'parents'
            ) {
                $mappedData[$map] = $data['c'][$element][0]['value'];
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
            'cTitle'       => './/title'
        );
        $descriptors = EADFileFormat::$descriptors;

        foreach ( $ead_mulitple_elements as $map=>$element ) {
            if ( array_key_exists($element, $data['c'])
                && count($data['c'][$element])
            ) {
                if (in_array($map, $descriptors) ) {
                    $mappedData['descriptors'][$map] = $data['c'][$element];
                } else {
                    $mappedData[$map] = $data['c'][$element];
                }
            }
        }

        //c elements order
        if ( isset($data['c']['order']) ) {
            $mappedData['elt_order'] = $data['c']['order'];
        }

        return $mappedData;
    }
}
