<?php
/**
 * Bach comments admin controller (for Sonata Admin)
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Bach\HomeBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Bach comments admin controller
 *
 * PHP version 5
 *
 * @category Security
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CommentAdminController extends Controller
{

    /**
     * Batch comment publication
     *
     * @param ProxyQueryInterface $selectedModelQuery ?
     *
     * @return void
     */
    public function batchActionPublish(ProxyQueryInterface $selectedModelQuery)
    {
        if (!$this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        $comments = $selectedModelQuery->execute();
        $modelManager = $this->admin->getModelManager();

        try {
            foreach ( $comments as $comment ) {
                $comment
                    ->setState(Comment::PUBLISHED)
                    ->setCloseDate(new \DateTime())
                    ->setClosedBy($this->getUser());

                $modelManager->update($comment);
            }
        } catch ( \Exception $e ) {
            $this->addFlash(
                'sonata_flash_error',
                _('An error occured trying to publish comments :(')
            );
        }

        $this->addFlash(
            'sonata_flash_success',
            _('Selected comments have been published')
        );

        return new RedirectResponse(
            $this->admin->generateUrl(
                'list',
                $this->admin->getFilterParameters()
            )
        );
    }
}
