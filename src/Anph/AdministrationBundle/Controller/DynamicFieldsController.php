<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\DynamicFieldsForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\DynamicFields;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class DynamicFieldsController extends Controller
{
    public function refreshAction()
    {
        $xmlP = new XMLProcess('core0');
        $form = $this->createForm(new DynamicFieldsForm(), new DynamicFields($xmlP));
        return $this->render('AdministrationBundle:Default:dynamicfields.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}
