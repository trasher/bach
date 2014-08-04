<?php
/**
 * Bach default administration controller
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Controller;

use Bach\AdministrationBundle\Entity\Helpers\ViewObjects\CoreStatus;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Bach\AdministrationBundle\Entity\SolrAdmin\Infos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Bach default administration controller
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class DefaultController extends Controller
{
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

        $configreader = $this->container->get('bach.administration.configreader');
        $sca = new SolrCoreAdmin($configreader);
        $coreNames = $sca->getStatus()->getCoreNames();
        $coresInfo = array();
        foreach ($coreNames as $cn) {
            $coresInfo[$cn] = new CoreStatus($sca, $cn);
        }
        $session = $this->getRequest()->getSession();
        $session->set('coreNames', $coreNames);

        $tmpCoreNames = $sca->getTempCoresNames();

        return $this->render(
            'AdministrationBundle:Default:dashboard.html.twig',
            array(
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
