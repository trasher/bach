<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\ViewObjects\CoreStatus;

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
    	    $form = $this->createForm(new CoreCreationForm($this->getAvailableCoreNames()));
	    } else {
	        $btn = $request->request->get('createCoreOk');
	        if (isset($btn)) {
	            $form = $this->createCoreAction($request, $session->get('xmlP'));
	        } elseif (isset($btn)) {
	            echo 'ELSIF';
	        }
	    }
    	return $this->render('AdministrationBundle:Default:coreadmin.html.twig', array(
    			'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames'),
    	        'coreStatus' => new CoreStatus($session->get('coreName'))
    	));
    }
    
    private function createCoreAction(Request $request, XMLProcess $xmlP)
    {
        $session = $request->getSession();
        $sca = new SolrCoreAdmin();
        $cc = new CoreCreation();
        $form = $this->createForm(new CoreCreationForm($this->getAvailableCoreNames()), $cc);
        $form->bind($request);
        $fields = $this->getFieldsFromDataBase($cc->core);
        $result = $sca->create($cc->core, $cc->core, $cc->core, $fields);
        if ($result != false && $result->isOk()) {
            $coreNames = $sca->getStatus()->getCoreNames();
            $session->set('coreNames', $coreNames);
        }
        return $form;
    }
    
    private function getFieldsFromDataBase($tableName)
    {
        $sql = "SELECT COLUMN_NAME AS name FROM information_schema.COLUMNS WHERE TABLE_NAME ='" . $tableName . "'";
        $connection = $this->getDoctrine()->getConnection();
        $result = $connection->query($sql);
        $res = array();
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
            $res[] = $row['name'];
        }
        return $res;
    }
    
    private function getAvailableCoreNames(SolrCoreAdmin $sca = null)
    {
        if ($sca == null) {
            $sca = new SolrCoreAdmin();
        }
        $runningCores = $sca->getStatus()->getCoreNames();
        $tableNames = $this->getTableNamesFromDataBase();
        $availableCores = array();
        foreach ($tableNames as $t) {
            $subStr = substr($t, strlen($t) - 6);
            if (!in_array($t, $runningCores) && $subStr === 'Format') {
                $availableCores[$t] = $t;
            }
        }
        return $availableCores;
    }
}
