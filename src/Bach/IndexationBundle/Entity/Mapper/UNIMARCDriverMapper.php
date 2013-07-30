<?php
/**
 * Mapper for UNIMARC data
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

/**
 * Mapper for UNIMARC data
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class UNIMARCDriverMapper implements DriverMapperInterface
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
        // $data est un objet Notice Unimarc
        $mappedData = array();
        $sticks = $data->getSticks();

        foreach ( $sticks as $stick ) {
            if ( $stick->getRef() == '001' ) { //mappage de headerId
                $mappedData['headerId'] = $stick->getArea()->getContent();
                $mappedData['archDescUnitId'] = $stick->getArea()->getContent();
            } else if ( $stick->getRef() == '100' ) {
                $subAreas = $stick->getArea()->getSubAreas();
                foreach ( $subAreas as $subArea ) {
                    if ( $subArea->getRef() == "a" ) { //mappage de headerDate
                        $ur = $subArea->getContent();
                        $mappedData['headerDate'] = substr($ur, 0, 7);
                    }
                }
            } else if ( $stick->getRef() == '101' ) {
                $subAreas = $stick->getArea()->getSubAreas();
                foreach ( $subAreas as $subArea ) {
                    if ( $subArea->getRef() == "a" ) { //mappage de headerDate
                        $ur = $subArea->getContent();
                        $mappedData['headerLanguage'] = $ur;
                    }
                }
            } else if ( $stick->getRef() == '200' ) {
                $subAreas = $stick->getArea()->getSubAreas();
                foreach ( $subAreas as $subArea ) {
                    if ( $subArea->getRef() == "a" ) {
                        $mappedData['headerTitle'] = $subArea->getContent();
                        $mappedData['archDescUnitTitle'] = $subArea->getContent();
                    }
                }
            } else if ( $stick->getRef() == '215' ) {
                $subAreas = $stick->getArea()->getSubAreas();
                foreach ( $subAreas as $subArea ) {
                    if ( $subArea->getRef() == "a" ) {
                        $mappedData['archDescExtent'] = $subArea->getContent();
                    } else if ( $subArea->getRef() == "c" ) {
                        $mappedData['archDescDimension'] = $subArea->getContent();
                    } else if ( $subArea->getRef() == "d" ) {
                        $mappedData['archDescDimension'] = $subArea->getContent();
                    }
                }
            } else if ( $stick->getRef() == '303' ) {
                $subAreas = $stick->getArea()->getSubAreas();
                foreach ( $subAreas as $subArea ) {
                    if ( $subArea->getRef() == "a" ) {
                        $mappedData['archDescScopeContent'] = $subArea->getContent();
                    }
                }
            } else if ( $stick->getRef() == '501' ) {
                $subAreas = $stick->getArea()->getSubAreas();
                foreach ( $subAreas as $subArea ) {
                    if ( $subArea->getRef() == "a" ) {
                        $mappedData['archDescArrangment'] = $subArea->getContent();
                    }
                }
            } else if ( $stick->getRef() == '700' ) {
                $subAreas = $stick->getArea()->getSubAreas();
                $mappedData["headerAuthor"] = $stick->getArea()->getContent();
                foreach ( $subAreas as $subArea ) {
                    if ( $subArea->getRef() == "a" ) {
                        if ( !array_key_exists('archDescRepository', $mappedData) ) {
                            $mappedData['archDescRepository'] = $subArea->getContent();
                        } else {
                            $mappedData['archDescRepository'] = $mappedData['archDescRepository']."  ;  ".$subArea->getContent();
                        }
                    } else if ( $subArea->getRef() == 'b' ) {
                        if ( !array_key_exists('archDescRepository', $mappedData) ) {
                            $mappedData['archDescRepository'] = $subArea->getContent();
                        } else {
                            $mappedData['archDescRepository'] = $mappedData['archDescRepository']."  ;  ".$subArea->getContent();
                        }
                    }
                }
            } else if ( $stick->getRef() == '710' ) {
                $subAreas = $stick->getArea()->getSubAreas();
                foreach ( $subAreas as $subArea ) {
                    if ( $subArea->getRef() == "a" ) {
                        $mappedData['archDescOrigination'] = $subArea->getContent();
                    }
                }
            } else if ( $stick->getRef() == '801') {
                $subAreas = $stick->getArea()->getSubAreas();
                foreach ( $subAreas as $subArea ) {
                    if ( $subArea->getRef() == "a" ) {
                        if ( !array_key_exists('archDescAcqInfo', $mappedData) ) {
                            $mappedData['archDescAcqInfo'] = $subArea->getContent();
                        } else {
                            $mappedData['archDescAcqInfo'] = $mappedData['archDescAcqInfo']."     ;   ".$subArea->getContent();
                        }
                    }
                }
            } else if ($stick->getRef() == '210') {
                $subAreas = $stick->getArea()->getSubAreas();
                foreach ($subAreas as $subArea ) {
                    if ( $subArea->getRef() == "c" ) {
                        $mappedData['headerPublisher'] = $subArea->getContent();
                    }
                }
            }
        }
        return $mappedData;
    }
}
