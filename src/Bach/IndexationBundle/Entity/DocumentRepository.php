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
use Doctrine\ORM\Tools\Pagination\Paginator;

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
     * @param int $page Requested page
     * @param int $show Rows to display
     *
     * @return array
     */
    public function getPublishedDocuments($page = 1, $show = 30)
    {
        $sql = 'SELECT d from BachIndexationBundle:Document d '.
            'LEFT JOIN d.task t WHERE t.taskId IS NULL or t.status=1 ' .
            'ORDER BY d.extension, d.id';
        $query = $this->getEntityManager()->createQuery($sql)
            ->setFirstResult(($page - 1) * $show)
            ->setMaxResults($show);

        $paginator = new Paginator($query, $fetchJoinCollection = false);
        return $paginator;
    }
}
