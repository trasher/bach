<?php

namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\ViewObjects\CoreStatus;

use Anph\AdministrationBundle\Entity\Dashboard\Dashboard;

use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\FieldsForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Fields;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

//use Anph\AdministrationBundle\Controller\XMLProcess;

class DefaultController extends Controller
{
	public function indexAction()
	{
		return $this->render('AdministrationBundle:Default:index.html.twig');
	}
	
	public function coreadminAction()
	{
		return $this->render('AdministrationBundle:Default:coreadmin.html.twig');
	}
	
	public function performanceAction()
	{
		return $this->render('AdministrationBundle:Default:performance.html.twig');
	}
	
	public function dashboardAction()
	{
	    $coreName = $this->getRequest()->request->get('selectedCore');
	    if (!isset($coreName)) {
	        $coreName = 'none';
	    }
	    $sca = new SolrCoreAdmin();
	    $coreNames = $sca->getStatus()->getCoreNames();
	    $coresInfo = array();
	    foreach ($coreNames as $cn) {
	        $coresInfo[$cn] = new CoreStatus($cn);
	    }
        $session = $this->getRequest()->getSession();
        $session->set('coreNames', $coreNames);
        $session->set('coreName', $coreName);
        if ($coreName == 'none') {
            $session->set('xmlP', null);
        } else {
            $session->set('xmlP', new XMLProcess($coreName));
        }
        
        $db = new Dashboard();
        
        $SystemFreeVirtualMemory=$db->getSystemFreeVirtualMemory();
        $SystemUsedVirtualMemory = $db->getSystemTotalVirtualMemory()-$SystemFreeVirtualMemory;
        $SystemFreeSwapMemory=$db->getSystemFreeSwapMemory();
        $SystemUsedSwapMemory=$db->getSystemTotalSwapMemory()-$SystemFreeSwapMemory;
        
        return $this->render('AdministrationBundle:Default:dashboard.html.twig', array(
                'coreName'  => $coreName,
                'coreNames' => $coreNames,
        		'SystemUsedVirtualMemory' => $SystemUsedVirtualMemory,
        		'SystemFreeVirtualMemory' => $SystemFreeVirtualMemory,
        		'SystemUsedSwapMemory' => $SystemUsedSwapMemory,
        		'SystemFreeSwapMemory' => $SystemFreeSwapMemory,
                'coresInfo' => $coresInfo
                ));
	}
}
