<?php
/**
 * Bach comments controller
 *
 * PHP version 5
 *
 * @category Security
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Bach\HomeBundle\Entity\Comment;
use Bach\HomeBundle\Form\Type\CommentType;
use Bach\IndexationBundle\Entity\EADFileFormat;

/**
 * Bach comments controller
 *
 * PHP version 5
 *
 * @category Security
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CommentsController extends Controller
{
    /**
     * Add a comment
     *
     * @param string $docid Document id
     *
     * @return void
     */
    public function addAction($docid)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $comment = new Comment();

        $repo = $this->getDoctrine()
            ->getRepository('BachIndexationBundle:EADFileFormat');
        $eadfile = $repo->findOneByFragmentid($docid);
        $comment->setEadfile($eadfile);

        //get user, if any
        $user = $this->getUser();
        if ( $user ) {
            $comment->setOpenedBy($user);
        }

        $form = $this->createForm(
            new CommentType(),
            $comment
        );

        $form->handleRequest($request);
        if ( $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Your comment has been stored. Thank you!')
            );
            return $this->redirect(
                $this->generateUrl(
                    'bach_display_document',
                    array(
                        'docid' => $docid
                    )
                )
            );
        } else {
            return $this->render(
                'BachHomeBundle:Comment:add.html.twig',
                array(
                    'docid'     => $docid,
                    'form'      => $form->createView(),
                    'eadfile'   => $eadfile
                )
            );
        }
    }
}
