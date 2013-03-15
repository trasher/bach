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
    	$xmlP = new XMLProcess("core0");
    	$form = $this->createForm(new CoreCreationForm($this->getTableNamesFromDataBase()));
    	
    	return $this->render('AdministrationBundle:Default:coreadmin.html.twig', array(
    			'form' => $form->createView(),
    	        'coreName' => 'core0'
    	));
    	
    	
    	
        return $this->render('AdministrationBundle:Default:coreadmin.html.twig');
    }
    
    public function createCoreAction($tableName)
    {
        /*$cc = new CoreCreation();
        $tableNames = $this->getTableNamesFromDataBase();
        $form = $this->createFormBuilder($cc, $tableNames)->getForm();*/
        $sca = new SolrCoreAdmin();
        $fields = $this->getFieldsFromDataBase($tableName);
        $sca->create('coreForTest', 'coreForTestDir', 'UniversalFileFormat', $fields);
        return $this->render('AdministrationBundle:Default:coreadmin.html.twig');
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
