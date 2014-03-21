<?php
/**
 * Mapper for PMB data
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity\Mapper;

use Bach\IndexationBundle\DriverMapperInterface;

/**
 * Mapper for PMB data
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
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

        $notices_elements = array(
            'idNotice' => 'idNotice',
            'codage' => 'zoneCodageUnimarc/codage',
            'titrePrincipal' => 'zoneTitre/titrePrincipal',
            'titrePropreAuteurDiffzoneTitre' => 'zoneTitre/titrePropreAuteurDiffzoneTitre',
            'titreParallele' => 'zoneTitre/titreParallele',
            'titreComplement' => 'zoneTitre/titreComplement',
            'prix' => 'prixISBN/s_d',
            'lien' => 'zoneLiens/lien',
            'eFormat' => 'zoneLiens/eFormat',
            'langueDocument' => 'zoneLangues/langueDocument',
            'langueOriginale' => 'zoneLangues/langueOriginale',
            'mention' => 'zoneMentionEdition/mention',
            'nbPages' => 'zoneCollation/nbPages',
            'illustration' => 'zoneCollation/illustration',
            'taille' => 'zoneCollation/taille',
            'materielAccompagnement' => 'zoneCollation/materielAccompagnement',
            'noteGenerale' => 'zoneNotes/noteGenerale',
            'noteContenu' => 'zoneNotes/noteContenu',
            'noteResume' => 'zoneNotes/noteResume',
            'zoneAuteursAutresnom' => 'zoneAuteursAutres/nom',
            'zoneAuteursAutresprenom' => 'zoneAuteursAutres/prenom',
            'zoneAuteursAutrescodeFonction' => 'zoneAuteursAutres/codeFonction',
            'zoneAuteursSecondairesnom' => 'zoneAuteursSecondaires/nom',
            'zoneAuteursSecondairesprenom' => 'zoneAuteursSecondaires/prenom',
            'zoneAuteursSecondairescodeFonction' => 'zoneAuteursSecondaires/codeFonction',
            'zoneAuteurPrincipalnom' => 'zoneAuteurPrincipal/nom',
            'zoneAuteurPrincipalprenom' => 'zoneAuteurPrincipal/prenom',
            'zoneAuteurPrincipalcodeFonction' => 'zoneAuteurPrincipal/codeFonction',
            'ville' => 'zoneEditeur/ville',
            's_b' => 'zoneEditeur/s_b',
            'zoneEditeurnom' => 'zoneEditeur/nom',
            'zoneEditeur/annee' => 'zoneEditeur/annee',
            '225/nom' => 'zoneCollection225/nom',
            'numDansCollection' => 'zoneCollection225/numDansCollection',
            'ISSN' => 'zoneCollection225/ISSN',
            '410nom' => 'zoneCollection410/nom',
            '410s_v' => 'zoneCollection410/s_v',
            's_t' => 'f_411/s_t',
            's_x' => 'f_411/s_x',
            'titre' => 'zoneMere/titre',
            'v' => 'zoneMere/numero',
            'zoneIndexationDecimalenom' => 'zoneIndexationDecimale/nom',
            'zoneIndexationDecimales_l' => 'zoneIndexationDecimale/s_l',
            'f_896s_a' => 'f_896/s_a',
            'mot' => 'zoneMotsClesLibres/mot',
            'categorie' => 'zoneCategories/categorie'
        );

        foreach ( $notices_elements as $map=>$element ) {

		    if ( array_key_exists($element, $data) ) {
		        $mapped_data[$map]= $data[$element];
		    }
		}
        return $mapped_data;
    }
}
