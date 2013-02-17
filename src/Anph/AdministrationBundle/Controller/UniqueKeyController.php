<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\UniqueKeyForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\UniqueKey;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class UniqueKeyController extends Controller
{
    public function refreshAction()
    {
        $xmlP = new XMLProcess('core0');
        $form = $this->createForm(new UniqueKeyForm(), new UniqueKey($xmlP));
        return $this->render('AdministrationBundle:Default:uniquekey.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}
