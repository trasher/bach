<?php
/**
 * Mapper for PMB data
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
 * Mapper for PMBz data
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class PMBDriverMapper implements DriverMapperInterface
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

        $notice_elements = array(
             'noticeId' => 'idNotice',
             'noticeCodage' => 'zoneCodageUnimarc/codage',
             'noticeTitrePrincipal' => 'zoneTitre/titrePrincipal',
             'noticeISBN' => 'prixISBN/ISBN',
             'noticeLangueDocument' => 'zoneLangues/langueDocument',
             'noticenbPages' => 'zoneCollation/nbPages',
             'noticeIllustration' => 'zoneCollation/illustration',
             'noticeTaille' => 'zoneCollation/taille',
             'noticeNoteContenu' => 'zoneNotes/noteContenu',
             'noticeAuteurPrincipalnom' => 'zoneAuteurPrincipal/nom',
             'noticeAuteurPrincipalprenom' => 'zoneAuteurPrincipal/prenom',
             'noticeAuteurPrincipalCodeFonctionv' => 'zoneAuteurPrincipal/codeFonctionv',
             'noticeAuteurPrincipals_9' => 'zoneAuteurPrincipal/s_9',
             'noticeville' => 'zoneEditeur/ville',
             'noticeEditeus_bv' => 'zoneEditeur/s_bv',
             'noticeEditeunom' => 'zoneEditeur/nom',
             'noticeEditeuannee' => 'zoneEditeur/annee',
             'noticeEditeus_9' => 'zoneEditeur/s_9',
             'notice225nom' => 'zoneCollection225/nom',
             'notice225s_9' => 'zoneCollection225/s_9',
             'notice410nom' => 'zoneCollection410/nom',
             'notice410s_9' => 'zoneCollection410/s_9',
             'noticeIndexationnom' => 'zoneIndexationDecimale/nom',
             'noticeIndexations_lv' => 'zoneIndexationDecimale/s_lv',
             'noticeIndexations_9' => 'zoneIndexationDecimale/s_9',
             'noticef_896s_a' => 'f_896/s_a',
             'noticecategorie' => 'zoneCategories/categorie'
        );
        return $mappedData;
    }
}
