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
            'codage_unimarc' => 'zoneCodageUnimarc/codage',
            'titre_propre' => 'zoneTitre/titrePrincipal',
            'titrepropre_auteur_different' => 'zoneTitre/titrePropreAuteurDiffzoneTitre',
            'titre_parallele' => 'zoneTitre/titreParallele',
            'titre_complement' => 'zoneTitre/titreComplement',
            'prix' => 'prixISBN/s_d',
            'link_ressource_electronque' => 'zoneLiens/lien',
            'format_elect_ressource' => 'zoneLiens/eFormat',
            'language' => 'zoneLangues',
            'mention_edition' => 'zoneMentionEdition/mention',
            'importance_materielle' => 'zoneCollation/nbPages',
            'autres_carac_materielle' => 'zoneCollation/illustration',
            'format' => 'zoneCollation/taille',
            'materiel_accompagnement' => 'zoneCollation/materielAccompagnement',
            'note_generale' => 'zoneNotes/noteGenerale',
            'note_content' => 'zoneNotes/noteContenu',
            'extract' => 'zoneNotes/noteResume',
            'authors' => 'zoneAuteursAutres|zoneAuteursSecondaires|zoneAuteurPrincipal',
            'autre_editeur' => 'zoneEditeur/s_b',
            'editeur' => 'zoneEditeur/nom',
            'year' => 'zoneEditeur/annee',
            'collection' => 'zoneCollection225/nom',
            'num_collection' => 'zoneCollection225/numDansCollection',
            'sous_collection' => 'f_411/s_t',
            'part_of' => 'zoneMere/titre',
            'part_num' => 'zoneMere/numero',
            'indexation_decimale' => 'zoneIndexationDecimale/s_l',
            'url_vignette' => 'f_896/s_a',
            'key_word' => 'zoneMotsClesLibres/mot',
            'category' => 'zoneCategories/categorie'
        );

        foreach ( $notices_elements as $map=>$element ) {

		    if ( array_key_exists($element, $data) ) {
		        $mapped_data[$map]= $data[$element];
		    }
		}
        return $mapped_data;
    }
}
