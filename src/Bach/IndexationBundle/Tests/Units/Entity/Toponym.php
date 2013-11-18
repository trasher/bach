<?php
/**
 * Bach Toponym unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
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
 * @license  Unknown http://unknown.com
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

        $values = array(
            'Nîmes',
            'Nîmes (Gard, France)',
            'Nîmes (France)',
            'Thouzon (Le Thor, Vaucluse, France)',
            'Crique Mouche (Cayenne, Guyane, France ; crique)',
            'montagne de Kaw (Guyane, France ; site naturel)',
            'Caderousse (Vaucluse, France) -- Portail du Pont',
            'Rhône (Suisse/France ; cours d\'eau) -- Brouteaux et créments',
            'Rivières, péage et port de (Caderousse, Vaucluse, France)',
            'Codolet, île de (Codolet, Gard, France)',
            'Languedoc (France ; province)'
        );
        $values_fail = array(
            /* Term into parenthesis will fail */
            'Soyouz (ELS) (Sinnamary, Guyane, France ; ensemble de lancement)',
            /* Unknown description */
            'Gévaudan (France ; baillage)'
        );

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
        $this->boolean($localizable)->isTrue();

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
        $this->boolean($localizable)->isTrue();

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
        $this->boolean($localizable)->isTrue();

    }
}
