<?php
/**
 * Bach defaults browse fields fixture
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
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bach\HomeBundle\Entity\BrowseFields;

/**
 * Bach defaults browse fields fixture
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class LoadDefaultBrowseFields implements FixtureInterface
{
    /**
     * Loads fixture
     *
     * @param ObjectManager $manager Object manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $defaults = array(
            array(
                'field'     => 'cSubject',
                'fr_label'  => 'Sujets',
                'en_label'  => 'Subject'
            ),
            array(
                'field'     => 'cPersname',
                'fr_label'  => 'Personnes',
                'en_label'  => 'People'
            ),
            array(
                'field'     => 'cGeogname',
                'fr_label'  => 'Lieux',
                'en_label'  => 'Places'
            )
        );

        for ( $i = 0; $i < count($defaults); $i++ ) {
            $fields = new BrowseFields();
            $data = $defaults[$i];
            $fields->setSolrFieldName($data['field']);
            $fields->setFrLabel($data['fr_label']);
            $fields->setEnLabel($data['en_label']);
            $fields->setActive(true);
            $fields->setPosition($i);
            $manager->persist($fields);
        }

        $manager->flush();
    }
}
