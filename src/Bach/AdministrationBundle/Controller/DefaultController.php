<?php

namespace Bach\AdministrationBundle\Controller;

use Bach\AdministrationBundle\Entity\Helpers\ViewObjects\CoreStatus;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Bach\AdministrationBundle\Entity\SolrAdmin\Infos;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * Administration index
     *
     * @return void
     */
    public function indexAction()
    {
        return $this->render('AdministrationBundle:Default:index.html.twig');
    }

    /**
     * Solr cores administration interface
     *
     * @return void
     */
    public function coreadminAction()
    {
        return $this->render('AdministrationBundle:Default:coreadmin.html.twig');
    }

    /**
     * Solr performance interface
     *
     * @return void
     */
    public function performanceAction()
    {
        return $this->render('AdministrationBundle:Default:performance.html.twig');
    }

    /**
     * Displays dashboard
     *
     * @return void
     */
    public function dashboardAction()
    {
        $solr_infos = new Infos(
            $this->container->getParameter('solr_ssl'),
            $this->container->getParameter('solr_host'),
            $this->container->getParameter('solr_port'),
            $this->container->getParameter('solr_path')
        );

        $solr_infos->loadSystemInfos();

        $coreName = $this->getRequest()->request->get('selectedCore');
        if (!isset($coreName)) {
            $coreName = 'none';
        }
        $configreader = $this->container->get('bach.administration.configreader');
        $sca = new SolrCoreAdmin($configreader);
        $coreNames = $sca->getStatus()->getCoreNames();
        $coresInfo = array();
        foreach ($coreNames as $cn) {
            $coresInfo[$cn] = new CoreStatus($sca, $cn);
        }
        $session = $this->getRequest()->getSession();
        $session->set('coreNames', $coreNames);
        $session->set('coreName', $coreName);
        if ($coreName == 'none') {
            $session->set('xmlP', null);
        } else {
            $session->set('xmlP', new XMLProcess($sca, $coreName));
        }

        $tmpCoreNames = $sca->getTempCoresNames();

        return $this->render(
            'AdministrationBundle:Default:dashboard.html.twig',
            array(
                'coreName'          => $coreName,
                'coreNames'         => $coreNames,
                'tmpCoresNames'     => $tmpCoreNames,
                'coresInfo'         => $coresInfo,
                'total_virt_mem'    => $solr_infos->getTotalVirtMem(),
                'used_virt_mem'     => $solr_infos->getUsedVirtMem(),
                'total_swap'        => $solr_infos->getTotalSwap(),
                'used_swap'         => $solr_infos->getUsedSwap(),
                'total_jvm'         => $solr_infos->getTotalJvmMem(),
                'used_jvm'          => $solr_infos->getUsedJvmMem(),
                'solr_version'      => $solr_infos->getSolrVersion(),
                'jvm_version'       => $solr_infos->getJvmInfos(),
                'system_version'    => $solr_infos->getSystemInfos(),
                'load_average'      => $solr_infos->getLoadAverage()
            )
        );
    }
}
