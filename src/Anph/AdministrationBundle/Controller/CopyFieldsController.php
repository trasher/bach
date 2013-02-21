<?php
namespace Anph\AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\CopyFields;
use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\CopyFieldsForm;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class CopyFieldsController extends Controller
{
    public function refreshAction()
    {
        $xmlP = new XMLProcess('core0');
        $fields =  new CopyFields($xmlP);
        $form = $this->createForm(new CopyFieldsForm(), $fields);
        return $this->render('AdministrationBundle:Default:copyfields.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}
