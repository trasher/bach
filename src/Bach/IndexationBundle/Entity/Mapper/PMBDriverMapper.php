<?php
/**
 * Mapper for PMB data
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
        $mapped_data = array();

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
            'category' => 'zoneCategories/categorie',
            'fragment' => 'fragment'
        );

        foreach ( $notices_elements as $map=>$element ) {

            if ( array_key_exists($element, $data) ) {
                $mapped_data[$map]= $data[$element];
            }
        }
        return $mapped_data;
    }
}
