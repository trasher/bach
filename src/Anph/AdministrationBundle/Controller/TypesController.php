<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Types;
use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\TypesForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class TypesController extends Controller
{
    public function refreshAction()
    {
        $xmlP = new XMLProcess('core0');
        $types =  new Types($xmlP);
        $form = $this->createForm(new TypesForm(), $types);
        return $this->render('AdministrationBundle:Default:fieldstype.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}