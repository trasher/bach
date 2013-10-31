<?php
/**
 * Bach expositions repository
 *
 * PHP version 5
 *
 * @category Expos
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\ExposBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ExpositionRepository
 *
 * @category Expos
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class ExpositionRepository extends EntityRepository
{

    /**
     * Retrieve current expositions (online and in the correct range of dates)
     *
     * @return array
     */
    public function findCurrent()
    {
        $now = new \DateTime();

        return $this->getEntityManager()
            ->createQuery(
                'SELECT e FROM ExposBundle:Exposition e 
                    WHERE e.online = :online 
                    AND e.beginDate <= :bdate
                    AND (e.endDate > :edate OR e.endDate IS NULL)'
            )->setParameters(
                array(
                    'online'    => true,
                    'bdate'     => $now,
                    'edate'     => $now
                )
            )->getResult();
    }
}
