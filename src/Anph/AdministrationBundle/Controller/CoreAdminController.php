<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\CoreCreationForm;

use Symfony\Component\HttpFoundation\Request;

use Anph\AdministrationBundle\Entity\Helpers\FormObjects\CoreCreation;

use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreAdminController extends Controller
{
    public function refreshAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->isMethod('GET')) {
    	    $form = $this->createForm(new CoreCreationForm($this->getTableNamesFromDataBase()));
	    } else {
	        $btn = $request->request->get('createCore');
	        if (isset($btn)) {
	            $form = $this->createCoreAction($request, $session->get('xmlP'));
	        } elseif (isset($btn)) {
	            echo 'ELSIF';
	        }
	    }
    	return $this->render('AdministrationBundle:Default:coreadmin.html.twig', array(
    			'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
    	));
    }
    
    private function createCoreAction(Request $request, XMLProcess $xmlP)
    {
        $session = $request->getSession();
        /*$cc = new CoreCreation();
        $tableNames = $this->getTableNamesFromDataBase();
        $form = $this->createFormBuilder($cc, $tableNames)->getForm();*/
        $cc = new CoreCreation();
        $form = $this->createForm(new CoreCreationForm($this->getTableNamesFromDataBase()), $cc);
        $form->bind($request);
        $sca = new SolrCoreAdmin();
        $fields = $this->getFieldsFromDataBase('UniversalFileFormat');
        $sca->create('coreForTest', 'coreForTestDir', 'UniversalFileFormat', $fields);
        return $this->refreshAction();
    }
    
    private function getFieldsFromDataBase($tableName)
    {
        $sql = "SELECT COLUMN_NAME AS name FROM information_schema.COLUMNS WHERE TABLE_NAME ='" . $tableName . "'";
        $connection = $this->getDoctrine()->getConnection();
        $result = $connection->query($sql);
        while ($row = $result->fetch()){
            $res[]=$row['name'];
        }
        return $res;
    }
    
    private function getTableNamesFromDataBase()
    {
        $sql = "SELECT table_name AS name FROM information_schema.tables WHERE table_schema LIKE 'bach'";
        $connection = $this->getDoctrine()->getConnection();
        $result = $connection->query($sql);
        $res = array();
        while ($row = $result->fetch()){
            $res[]=$row['name'];
        }
        return $res;
    }
}
