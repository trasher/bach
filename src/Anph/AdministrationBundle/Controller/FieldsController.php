<?php
namespace Anph\AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\FieldsForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Fields;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class FieldsController extends Controller
{
    public function refreshAction()
    {
        $xmlP = new XMLProcess('core0');
        $fields =  new Fields($xmlP);
        $form = $this->createForm(new FieldsForm(), $fields);
        return $this->render('AdministrationBundle:Default:fields.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}
