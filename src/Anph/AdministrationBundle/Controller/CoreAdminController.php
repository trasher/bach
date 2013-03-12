<?php
namespace Anph\AdministrationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Anph\AdministrationBundle\Entity\Helpers\FormObjects\CoreCreation;

use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreAdminController extends Controller
{
    public function refreshAction(Request $request)
    {
        return $this->render('AdministrationBundle:Default:coreadmin.html.twig');
    }
    
    public function createCoreAction()
    {
        /*$cc = new CoreCreation();
        $tableNames = $this->getTableNamesFromDataBase();
        $form = $this->createFormBuilder($cc, $tableNames)->getForm();*/
        $sca = new SolrCoreAdmin();
        $fields = $this->getFieldsFromDataBase('UniversalFileFormat');
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
        $sql = "SELECT table_name AS name FROM TABLES WHERE TABLE_SCHEMA LIKE 'SolrConfig_DB'";
        $connection = $this->getDoctrine()->getConnection();
        $result = $connection->query($sql);
        while ($row = $result->fetch()){
            $res[]=$row;
        }
        return $res;
    }
}
