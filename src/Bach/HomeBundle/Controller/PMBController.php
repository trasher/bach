<?php
/**
 * Bach PMB controller
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
 * @category Search
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Bach\HomeBundle\Form\Type\MatriculesType;
use Bach\HomeBundle\Entity\SolariumQueryContainer;
use Bach\HomeBundle\Entity\Filters;
use Bach\HomeBundle\Entity\ViewParams;
use Bach\HomeBundle\Entity\GeolocFields;
use Bach\HomeBundle\Entity\Facets;
use Bach\HomeBundle\Entity\SearchQueryFormType;
use Bach\HomeBundle\Entity\SearchQuery;

/**
 * Bach PMB controller
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

class PMBController extends SearchController
{
    //protected $date_field = 'date_enregistrement';

    /**
     * Default page
     *
     * @return void
     */
    public function indexAction()
    {
        $redirectUrl = $this->get('router')->generate('bach_pmb_search');
        return new RedirectResponse($redirectUrl);
    }

    /**
     * Search page
     *
     * @param string $query_terms Term(s) we search for
     * @param int    $page        Page
     * @param string $facet_name  Display more terms in suggests
     *
     * @return void
     */
    public function searchAction($query_terms = null, $page = 1,
        $facet_name = null
    ) {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ( $query_terms !== null ) {
            $query_terms = urldecode($query_terms);
        }

        /** Manage view parameters */
        $view_params = $this->handleViewParams();

        //set current view parameters according to request
        //$view_params->bind($request);

        //store new view parameters
        $session->set('pmb_view_params', $view_params);

        $tpl_vars = $this->searchTemplateVariables($view_params, $page);

        $filters = $session->get($this->getFiltersName());
        if ( !$filters instanceof Filters || $request->get('clear_filters') ) {
            $filters = new Filters();
            $session->set($this->getFiltersName(), null);
        }

        $filters->bind($request);
        $session->set($this->getFiltersName(), $filters);

        if ( ($request->get('filter_field') || $filters->count() > 0)
            && is_null($query_terms)
        ) {
            $query_terms = '*:*';
        }

        if ( $view_params->advancedSearch() ) {
            $form = $this->createForm(
                new PMBType(),
                null
            );
            $tpl_vars['search_path'] = 'bach_pmb_search';
        } else {
            $form = $this->createForm(
                new SearchQueryFormType($query_terms),
                null
            );
            $tpl_vars['search_path'] = 'bach_pmb_do_search';
        }

        $form->handleRequest($request);
        $data = $form->getData();

        $resultCount = null;
        $searchResults = null;

        $factory = $this->get($this->factoryName());
        $factory->setGeolocFields($this->getGeolocFields());
        //$factory->setDateField($this->date_field);

        if ( $filters->count() > 0 ) {
            $tpl_vars['filters'] = $filters;
        }

        if ( $view_params->advancedSearch() && count($data) > 0
            || !$view_params->advancedSearch() && $query_terms !== null
        ) {
            $container = new SolariumQueryContainer();

            if ( $view_params->advancedSearch() ) {
                $container->setField($this->getContainerFieldName(), $data);
            } else {
                $container->setField($this->getContainerFieldName(), $query_terms);
            }

            $container->setField(
                "pager",
                array(
                    "start"     => ($page - 1) * $view_params->getResultsbyPage(),
                    "offset"    => $view_params->getResultsbyPage()
                )
            );

            $container->setFilters($filters);
            $factory->prepareQuery($container);

            $conf_facets = array();
            $fields = array(
                'dyndescr_authtype_primary'    => _('Authors'),
                'category'                     => _('Category'),
                'language'                     => _('Language'),
                'collection'                   => _('Collection'),
                'editor'                       => _('Editor'),
                'year'                         => _('Year'),
                'indexing_decimal'             => _('Indexing decimal'),
                'format'                       => _('Format')
            );
            foreach ( $fields as $field_name=>$trad ) {
                $facet = new Facets();
                $facet->setSolrFieldName($field_name);
                $facet->setFrLabel($trad);
                $conf_facets[] = $facet;
            }

            $searchResults = $factory->performQuery(
                $container,
                $conf_facets
            );

            $hlSearchResults = $factory->getHighlighting();
            $scSearchResults = $factory->getSpellcheck();
            $resultCount = $searchResults->getNumFound();

            $tpl_vars['searchResults'] = $searchResults;
            $tpl_vars['hlSearchResults'] = $hlSearchResults;
            $tpl_vars['scSearchResults'] = $scSearchResults;
            $tpl_vars['totalPages'] = ceil(
                $resultCount/$view_params->getResultsbyPage()
            );

            $this->handleFacets(
                $factory,
                $conf_facets,
                $searchResults,
                $filters,
                $facet_name,
                $tpl_vars
            );

            $suggestions = null;
            if ( $view_params->advancedSearch() ) {
                $suggestions = $factory->getSuggestions(implode(' ', $data));
            } else {
                $suggestions = $factory->getSuggestions($query_terms);
            }

            //$this->handleYearlyResults($factory, $tpl_vars);
        }

        $slider_dates = $factory->getSliderDates(new Filters());

        if ( is_array($slider_dates) ) {
            $tpl_vars = array_merge($tpl_vars, $slider_dates);
        }

        /*$this->handleGeoloc(
            $factory,
            $tpl_vars
        );*/

        if ( $view_params->advancedSearch() ) {
            $tpl_vars['adv_form'] = $form->createView();
        } else {
            $tpl_vars['form'] = $form->createView();
        }

        //$tpl_vars['has_advanced'] = true;

        $tpl_vars['resultStart'] = ($page - 1)
            * $view_params->getResultsbyPage() + 1;
        $resultEnd = ($page - 1) * $view_params->getResultsbyPage()
            + $view_params->getResultsbyPage();
        if ( $resultEnd > $resultCount ) {
            $resultEnd = $resultCount;
        }
        $tpl_vars['resultEnd'] = $resultEnd;

        return $this->render(
            'BachHomeBundle:PMB:index.html.twig',
            array_merge(
                $tpl_vars,
                array(
                    'resultCount'   => $resultCount,
                    'q'             => urlencode($query_terms),
                )
            )
        );
    }

    /**
     * Get Solarium EntryPoint
     *
     * @return string
     */
    protected function entryPoint()
    {
        return 'solarium.client.pmb';
    }

    /**
     * Get factory name
     *
     * @return string
     */
    protected function factoryName()
    {
        return 'bach.pmb.solarium_query_factory';
    }

    /**
     * Get map facets session name
     *
     * @return string
     */
    protected function mapFacetsName()
    {
        return 'pmb_map_facets';
    }

    /**
     * Get date fields
     *
     * @return array
     */
    protected function getFacetsDateFields()
    {
        return array(

        );
    }

    /**
     * Get golocalization fields class name
     *
     * @return string
     */
    protected function getGeolocClass()
    {
        return 'Bach\HomeBundle\Entity\GeolocPMBFields';
    }

    /**
     * POST search destination for main form.
     *
     * Will take care of search terms, and reroute with proper URI
     *
     * @return void
     */
    public function doSearchAction()
    {
        $query = new SearchQuery();
        $form = $this->createForm(new SearchQueryFormType(), $query);
        $redirectUrl = $this->get('router')->generate('bach_pmb');

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $q = $query->getQuery();
                $redirectUrl = $this->get('router')->generate(
                    'bach_pmb_search',
                    array('query_terms' => $q)
                );

                $session = $this->getRequest()->getSession();
                $session->set($this->getFiltersName(), null);
            }
        }
        return new RedirectResponse($redirectUrl);
    }
    /**
      * Function to check a website status
      *
      * @param string $url the url of the website to test
      *
      * @return boolean true if the site is up, false otherwise
      */
     
    function checkStatus($url)
    {
         $agent = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; pt-pt) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27";
     
         // initializes curl session
         $ch=curl_init();
     
         // sets the URL to fetch
         curl_setopt($ch, CURLOPT_URL, $url);
     
         // sets the content of the User-Agent header
         curl_setopt($ch, CURLOPT_USERAGENT, $agent);
     
         // return the transfer as a string
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     
         // disable output verbose information
         curl_setopt($ch, CURLOPT_VERBOSE, false);
     
         // max number of seconds to allow cURL function to execute
         curl_setopt($ch, CURLOPT_TIMEOUT, 5);
     
         curl_exec($ch);
     
         // get HTTP response code
         $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
     
         curl_close($ch);
     
         if($httpcode>=200 && $httpcode<300)
              return true;
         else
              return false;
    }


    
    /**
     * Document display
     *
     * @param int     $docid Document unique identifier
     * @param int     $page  Page
     * @param boolean $ajax  Called from ajax
     *
     * @return void
     */
    public function displayDocumentAction($docid, $page = 1, $ajax = false)
    {
        $client = $this->get($this->entryPoint());
        $query = $client->createSelect();
        $query->setQuery('uniqid:"' . $docid . '"');
        $query->setStart(0)->setRows(1);

        $rs = $client->select($query);

        if ( $rs->getNumFound() !== 1 ) {
            throw new \RuntimeException(
                str_replace(
                    '%count%',
                    $rs->getNumFound(),
                    _('%count% results found, 1 expected.')
                )
            );
        }

        $docs  = $rs->getDocuments();
        $doc = $docs[0];
        $children = array();

        $tpl = '';

        $tplParams = $this->commonTemplateVariables();
        $tplParams = array_merge(
            $tplParams,
            array(
                'uniqid'        => $docid,
                'document'      => $doc
            )
        );

        if ( $ajax === 'ajax' ) {
            $tpl = 'BachHomeBundle:PMB:content_display.html.twig';
            $tplParams['ajax'] = true;
        } else {
            $tpl = 'BachHomeBundle:PMB:display.html.twig';
            $tplParams['ajax'] = false;
        }
        return $this->render(
            $tpl,
            $tplParams
        );
    }
    /**
     * Get available ordering options
     *
     * @return array
     */
    protected function getOrders()
    {
        $orders = array();
        return $orders;
    }

    /**
     * Get available views
     *
     * @return array
     */
    protected function getViews()
    {
        $views = array(
            'list'      => array(
                'text'  => _('List'),
                'title' => _('View search results as a list')
            ),
            'thumbs'    => array(
                'text'  => _('Thumbnails'),
                'title' => _('View search results as thumbnails')
            )
        );
        return $views;
    }

    /**
     * Get unique conf facet
     *
     * @param string $name Facet name
     *
     * @return array
     */
    protected function getUniqueFacet($name)
    {
        $conf_facets = array();

        $fields = array(
            'dyndescr_authtype_primary'    => _('Authors'),
            'category'                     => _('Category'),
            'language'                     => _('Language'),
            'collection'                   => _('Collection'),
            'editor'                       => _('Editor'),
            'year'                         => _('Year'),
            'indexing_decimal'             => _('Indexing decimal'),
            'format'                       => _('Format')
        );

        $facet = new Facets();
        $facet->setSolrFieldName($name);
        $facet->setFrLabel($fields[$name]);
        $conf_facets[] = $facet;

        return $conf_facets;
    }

    /**
     * Get container field name
     *
     * @return string
     */
    protected function getContainerFieldName()
    {
        return 'pmb';
    }

    /**
     * Get filters session name
     *
     * @return string
     */
    protected function getFiltersName()
    {
        return 'pmb_filters';
    }

    /**
     * Get search URI
     *
     * @return string
     */
    protected function getSearchUri()
    {
        return 'bach_pmb_search';
    }

        /**
     * Get view params service name
     *
     * @return string
     */
    protected function getViewParamsServicename()
    {
        return ('bach.home.pmb_view_params');
    }

    /**
     * Get session name for view parameters
     *
     * @return string
     */
    protected function getParamSessionName()
    {
        return 'pmb_view_params';
    }
}
?>
