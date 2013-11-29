<?php
/**
 * Document repository
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Document repository
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DocumentRepository extends EntityRepository
{

    /**
     * Retrieve published document list
     *
     * @return array
     */
    public function getPublishedDocuments()
    {
        $query = 'SELECT d from BachIndexationBundle:Document d '.
            'LEFT JOIN d.task t WHERE t.taskId IS NULL or t.status=1';
        $results = $this->getEntityManager()->createQuery($query)->getResult();
        return $results;
    }
}
