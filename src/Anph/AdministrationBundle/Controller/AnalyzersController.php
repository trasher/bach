<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\AnalyzersForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Analyzers;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AnalyzersController extends Controller
{
    public function refreshAction()
    {
        $xmlP = new XMLProcess('core0');
        $fields =  new Analyzers($xmlP);
        $form = $this->createForm(new AnalyzersForm(), $fields);
        return $this->render('AdministrationBundle:Default:analyzers.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}
