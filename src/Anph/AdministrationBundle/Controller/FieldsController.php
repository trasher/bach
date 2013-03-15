<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\FieldsForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Fields;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class FieldsController extends Controller
{
    public function refreshAction()
    {
        $xmlP = new XMLProcess('core0');
        $fd =  new Fields($xmlP);
        $form = $this->createForm(new FieldsForm(), $fd);
        return $this->render('AdministrationBundle:Default:fields.html.twig', array(
                'form' => $form->createView(),
        ));
    }
    
    public function submitAction(Request $request)
    {
        $fd = new Fields();
        $form = $this->createForm(new FieldsForm(), $fd);
        if ($request->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                // We save the modifications into the schema.xml file of corresponding core
                $xmlP = new XMLProcess('core0');
                $fd->save($xmlP);
            }
        }
        return $this->render('AdministrationBundle:Default:fields.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}
