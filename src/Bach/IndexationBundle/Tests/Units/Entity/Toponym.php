<?php
/**
 * Bach Toponym unit tests
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
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Tests\Units\Entity;

use atoum\AtoumBundle\Test\Units;
use Bach\IndexationBundle\Entity\Toponym as Entity;

/**
 * Bach Toponym unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class Toponym extends Units\Test
{

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstruct()
    {
        $ent = new Entity();

        $this->object($ent)->isInstanceOf('Bach\IndexationBundle\Entity\Toponym');

        $type = $ent->getType();
        $this->variable($type)->isIdenticalTo($ent::TYPE_TOWN);
    }

    /**
     * Test parser
     *
     * @return void
     */
    public function testParse()
    {
        $test = new Entity();
        $test->parse('Nîmes');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_TOWN);
        $this->string($name)->isIdenticalTo('Nîmes');
        $this->variable($specific)->isNull();
        $this->variable($country)->isNull();
        $this->variable($county)->isNull();
        $this->variable($nomination)->isNull();
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();

        $test = new Entity('Nîmes');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_TOWN);
        $this->string($name)->isIdenticalTo('Nîmes');
        $this->variable($specific)->isNull();
        $this->variable($country)->isNull();
        $this->variable($county)->isNull();
        $this->variable($nomination)->isNull();
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();

        $test = new Entity();
        $test->parse('Nîmes (France)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_TOWN);
        $this->string($name)->isIdenticalTo('Nîmes');
        $this->variable($specific)->isNull();
        $this->string($country)->isIdenticalTo('France');
        $this->variable($county)->isNull();
        $this->variable($nomination)->isNull();
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();

        $test = new Entity();
        $test->parse('Nîmes (Gard, France)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_TOWN);
        $this->string($name)->isIdenticalTo('Nîmes');
        $this->variable($specific)->isNull();
        $this->string($country)->isIdenticalTo('France');
        $this->string($county)->isIdenticalTo('Gard');
        $this->variable($nomination)->isNull();
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();

        $test = new Entity();
        $test->parse('Thouzon (Le Thor, Vaucluse, France)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_SPECIFIC);
        $this->string($name)->isIdenticalTo('Le Thor');
        $this->string($specific)->isIdenticalTo('Thouzon');
        $this->string($country)->isIdenticalTo('France');
        $this->string($county)->isIdenticalTo('Vaucluse');
        $this->variable($nomination)->isNull();
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();

        $test = new Entity();
        $test->parse('montagne de Kaw (Guyane, France ; site naturel)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_NOMINATED);
        $this->variable($name)->isNull();
        $this->string($specific)->isIdenticalTo('montagne de Kaw');
        $this->string($country)->isIdenticalTo('France');
        $this->string($county)->isIdenticalTo('Guyane');
        $this->string($nomination)->isIdenticalTo('site naturel');
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isFalse();

        $test = new Entity();
        $test->parse('Crique Mouche (Cayenne, Guyane, France ; crique)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_NOMINATED);
        $this->string($name)->isIdenticalTo('Cayenne');
        $this->string($specific)->isIdenticalTo('Crique Mouche');
        $this->string($country)->isIdenticalTo('France');
        $this->string($county)->isIdenticalTo('Guyane');
        $this->string($nomination)->isIdenticalTo('crique');
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isFalse();

        $test = new Entity();
        $test->parse('Languedoc (France ; province)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_NOMINATED);
        $this->variable($name)->isNull();
        $this->string($specific)->isIdenticalTo('Languedoc');
        $this->string($country)->isIdenticalTo('France');
        $this->variable($county)->isNull();
        $this->string($nomination)->isIdenticalTo('province');
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isFalse();

        $test = new Entity();
        $test->parse('Avignon (Vaucluse, France ; paroisse Saint-Pierre)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_NOMINATED);
        $this->variable($name)->isNull();
        $this->string($specific)->isIdenticalTo('Avignon');
        $this->string($country)->isIdenticalTo('France');
        $this->string($county)->isIdenticalTo('Vaucluse');
        $this->string($nomination)->isIdenticalTo('paroisse Saint-Pierre');
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isFalse();

        $test = new Entity();
        $test->parse('Caderousse (Vaucluse, France) -- Rue Droite');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_TOWN);
        $this->string($name)->isIdenticalTo('Caderousse');
        $this->variable($specific)->isNull();
        $this->string($country)->isIdenticalTo('France');
        $this->string($county)->isIdenticalTo('Vaucluse');
        $this->variable($nomination)->isNull();
        $this->string($subdivision)->isIdenticalTo('Rue Droite');
        $this->boolean($localizable)->isFalse();

        $test = new Entity();
        $test->parse('Gévaudan (France ; baillage)');

        $localizable = $test->canBeLocalized();
        $this->boolean($localizable)->isFalse();

        $test = new Entity();
        $test->parse('Soyouz (ELS) (Sinnamary, Guyane, France ; ensemble de lancement)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_NOMINATED);
        $this->string($name)->isIdenticalTo('Sinnamary');
        $this->string($specific)->isIdenticalTo('Soyouz');
        $this->string($country)->isIdenticalTo('France');
        $this->string($county)->isIdenticalTo('Guyane');
        $this->string($nomination)->isIdenticalTo('ensemble de lancement');
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isFalse();

        $test = new Entity();
        $test->parse('Paris');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_TOWN);
        $this->string($name)->isIdenticalTo('Paris');
        $this->variable($specific)->isNull();
        $this->variable($country)->isNull();
        $this->variable($county)->isNull();
        $this->variable($nomination)->isNull();
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();

        $test = new Entity();
        $test->parse('Niger');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_COUNTRY);
        $this->string($name)->isIdenticalTo('Niger');
        $this->variable($specific)->isNull();
        $this->string($country)->isIdenticalTo('Niger');
        $this->variable($county)->isNull();
        $this->variable($nomination)->isNull();
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();

        $test = new Entity();
        $test->parse('Niger (France)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_TOWN);
        $this->string($name)->isIdenticalTo('Niger');
        $this->variable($specific)->isNull();
        $this->string($country)->isIdenticalTo('France');
        $this->variable($county)->isNull();
        $this->variable($nomination)->isNull();
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();

        $test = new Entity('Ouest (Haïti ; département)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_STATE);
        $this->string($name)->isIdenticalTo('Ouest');
        $this->variable($specific)->isNull();
        $this->string($country)->isIdenticalTo('Haïti');
        $this->variable($county)->isNull();
        $this->string($nomination)->isIdenticalTo('département');
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();

        $test = new Entity();
        $test->parse('Nîmes (Gard, France ; commune)');

        $type = $test->getType();
        $name = $test->getName();
        $specific = $test->getSpecificName();
        $country = $test->getCountry();
        $county = $test->getCounty();
        $nomination = $test->getNomination();
        $subdivision = $test->getSubdivision();
        $localizable = $test->canBeLocalized();

        $this->variable($type)->isIdenticalTo($test::TYPE_TOWN);
        $this->string($name)->isIdenticalTo('Nîmes');
        $this->variable($specific)->isNull();
        $this->string($country)->isIdenticalTo('France');
        $this->string($county)->isIdenticalTo('Gard');
        $this->variable($nomination)->isIdenticalTo('commune');
        $this->variable($subdivision)->isNull();
        $this->boolean($localizable)->isTrue();
    }
}
