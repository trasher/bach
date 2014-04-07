<?php
/**
 * Bach home controller
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
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Controller;

use Bach\HomeBundle\Entity\SolariumQueryContainer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Bach\HomeBundle\Entity\SearchQueryFormType;
use Bach\HomeBundle\Entity\SearchQuery;
use Bach\HomeBundle\Entity\Comment;
use Bach\HomeBundle\Entity\Filters;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bach home controller
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class DefaultController extends SearchController
{
    protected $date_field = 'cDateBegin';

    /**
     * Default page
     *
     * @param string $form_name Search form name
     *
     * @return void
     */
    public function indexAction($form_name = null)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ( $form_name !== 'default' ) {
            $this->search_form = $form_name;
        }

        /** Manage view parameters */
        $view_params = $this->handleViewParams();

        $tpl_vars = $this->searchTemplateVariables($view_params);
        $session->set($this->getFiltersName(), null);

        $form = $this->createForm(
            new SearchQueryFormType(),
            new SearchQuery()
        );
        $tpl_vars['form'] = $form->createView();

        $factory = $this->get($this->factoryName());
        $factory->setDateField($this->date_field);

        $search_form_params = null;
        if ( $this->search_form !== null ) {
            $search_forms = $this->container->getParameter('search_forms');
            $search_form_params = $search_forms[$this->search_form];
        }

        $show_tagcloud = $this->container->getParameter('feature.tagcloud');
        if ( $show_tagcloud ) {
            $tagcloud = $factory->getTagCloud(
                $this->getDoctrine()->getManager(),
                $search_form_params
            );

            if ( $tagcloud ) {
                $tpl_vars['tagcloud'] = $tagcloud;
            }
        }

        $this->handleGeoloc($factory);

        $slider_dates = $factory->getSliderDates(new Filters(), $search_form_params);
        if ( is_array($slider_dates) ) {
            $tpl_vars = array_merge($tpl_vars, $slider_dates);
        }
        $this->handleYearlyResults($factory, $tpl_vars);

        return $this->render(
            'BachHomeBundle:Default:index.html.twig',
            $tpl_vars
        );
    }

    /**
     * Get Solarium EntryPoint
     *
     * @return string
     */
    protected function entryPoint()
    {
        return 'solarium.client';
    }

    /**
     * Get factory name
     *
     * @return string
     */
    protected function factoryName()
    {
        return 'bach.home.solarium_query_factory';
    }

    /**
     * Get map facets session name
     *
     * @return string
     */
    protected function mapFacetsName()
    {
        $name = 'map_facets';
        if ( $this->search_form !== null ) {
            $name .= '_form_' . $this->search_form;
        }
        return $name;
    }

    /**
     * Get date fields
     *
     * @return array
     */
    protected function getFacetsDateFields()
    {
        return array('cDate');
    }

    /**
     * Get golocalization fields class name
     *
     * @return string
     */
    protected function getGeolocClass()
    {
        return 'Bach\HomeBundle\Entity\GeolocMainFields';
    }

    /**
     * Search page
     *
     * @param string $query_terms Term(s) we search for
     * @param int    $page        Page
     * @param string $facet_name  Display more terms in suggests
     * @param string $form_name   Search form name
     *
     * @return void
     */
    public function searchAction($query_terms = null, $page = 1,
        $facet_name = null, $form_name = null
    ) {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ( $query_terms !== null ) {
            $query_terms = urldecode($query_terms);
        }

        if ( $form_name !== 'default' ) {
            $this->search_form = $form_name;
        }

        /** Manage view parameters */
        $view_params = $this->handleViewParams();

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
        } else if ( $query_terms === null && $filters->count() == 0 ) {
            $redirectUrl = null;
            if ( $this->search_form !== null ) {
                $redirectUrl = $this->get('router')->generate(
                    'bach_search_form_homepage',
                    array(
                        'form_name' => $this->search_form
                    )
                );
            } else {
                $redirectUrl = $this->get('router')->generate('bach_homepage');
            }
            return new RedirectResponse($redirectUrl);
        }

        $templateVars = $this->searchTemplateVariables($view_params, $page);
        $templateVars = array_merge(
            $templateVars,
            array(
                'q'             => urlencode($query_terms),
                'show_pics'     => $view_params->showPics(),
                'show_map'      => $view_params->showMap(),
                'show_daterange'=> $view_params->showDaterange(),
                'view'          => $view_params->getView(),
                'results_order' => $view_params->getOrder()
            )
        );

        $factory = $this->get($this->factoryName());
        $factory->setGeolocFields($this->getGeolocFields());
        $factory->setDateField($this->date_field);

        // On effectue une recherche
        $form = $this->createForm(
            new SearchQueryFormType($query_terms),
            new SearchQuery()
        );

        $container = new SolariumQueryContainer();
        $container->setOrder($view_params->getOrder());

        $search_forms = null;
        if ( $this->search_form !== null ) {
            $search_forms = $this->container->getParameter('search_forms');
            $container->setSearchForm($search_forms[$this->search_form]);
        }

        $container->setField(
            'show_pics',
            $view_params->showPics()
        );
        $container->setField($this->getContainerFieldName(), $query_terms);

        $container->setField(
            "pager",
            array(
                "start"     => ($page - 1) * $view_params->getResultsbyPage(),
                "offset"    => $view_params->getResultsbyPage()
            )
        );

        //Add filters to container
        $container->setFilters($filters);
        if ( $filters->count() > 0 ) {
            $templateVars['filters'] = $filters;
        }

        $factory->prepareQuery($container);

        $search_form_params = null;
        if ( $search_forms !== null ) {
            $search_form_params = $search_forms[$this->search_form];
        }

        $current_form = 'main';
        if ( $search_form_params !== null ) {
            $current_form = $this->search_form;
        }
        $conf_facets = $this->getDoctrine()
            ->getRepository('BachHomeBundle:Facets')
            ->findBy(
                array(
                    'active'    => true,
                    'form'      => $current_form
                ),
                array('position' => 'ASC')
            );

        $searchResults = $factory->performQuery(
            $container,
            $conf_facets
        );

        $hlSearchResults = $factory->getHighlighting();
        $scSearchResults = $factory->getSpellcheck();
        $resultCount = $searchResults->getNumFound();

        $this->handleFacets(
            $factory,
            $conf_facets,
            $searchResults,
            $filters,
            $facet_name,
            $templateVars
        );
        $suggestions = $factory->getSuggestions($query_terms);

        $templateVars['resultCount'] = $resultCount;
        $templateVars['resultByPage'] = $view_params->getResultsbyPage();
        $templateVars['totalPages'] = ceil(
            $resultCount/$view_params->getResultsbyPage()
        );
        $templateVars['searchResults'] = $searchResults;
        $templateVars['hlSearchResults'] = $hlSearchResults;
        $templateVars['scSearchResults'] = $scSearchResults;
        $templateVars['resultStart'] = ($page - 1)
            * $view_params->getResultsbyPage() + 1;
        $resultEnd = ($page - 1) * $view_params->getResultsbyPage()
            + $view_params->getResultsbyPage();
        if ( $resultEnd > $resultCount ) {
            $resultEnd = $resultCount;
        }
        $templateVars['resultEnd'] = $resultEnd;

        $slider_dates = $factory->getSliderDates($filters, $search_form_params);
        if ( is_array($slider_dates) ) {
            $templateVars = array_merge($templateVars, $slider_dates);
        }

        $this->handleYearlyResults($factory, $templateVars);

        $templateVars['form'] = $form->createView();

        if ( isset($suggestions) && $suggestions->count() > 0 ) {
            $templateVars['suggestions'] = $suggestions;
        }

        return $this->render(
            'BachHomeBundle:Default:index.html.twig',
            $templateVars
        );
    }

    /**
     * POST search destination for main form.
     *
     * Will take care of search terms, and reroute with proper URI
     *
     * @param string $form_name Search form name
     *
     * @return void
     */
    public function doSearchAction($form_name = null)
    {
        if ( $form_name !== 'default' ) {
            $this->search_form = $form_name;
        }
        $query = new SearchQuery();
        $form = $this->createForm(new SearchQueryFormType(), $query);

        $redirectUrl = null;
        if ( $this->search_form !== null ) {
            $redirectUrl = $this->get('router')->generate(
                'bach_search_form_homepage',
                array(
                    'form_name' => $this->search_form
                )
            );
        } else {
            $redirectUrl = $this->get('router')->generate('bach_homepage');
        }

        $request = $this->getRequest();

        if ( $request->isMethod('POST') ) {
            $form->bind($request);
            if ($form->isValid()) {
                $q = $query->getQuery();
                $url_vars = array('query_terms' => $q);

                $session = $request->getSession();
                $session->set($this->getFiltersName(), null);

                //check for filtering informations
                if ( $request->get('filter_field')
                    && $request->get('filter_value')
                ) {
                    $url_vars['filter_field'] = $request->get('filter_field');
                    $url_vars['filter_value'] = $request->get('filter_value');
                }

                $route = 'bach_search';
                if ( $this->search_form !== null ) {
                    $url_vars['form_name'] = $this->search_form;
                }

                $redirectUrl = $this->get('router')->generate(
                    $route,
                    $url_vars
                );
            }
        }
        return new RedirectResponse($redirectUrl);
    }

    /**
     * Browse contents
     *
     * @param string  $part Part to browse
     * @param boolean $ajax If we were called from ajax
     *
     * @return void
     */
    public function browseAction($part = '', $ajax = false)
    {
        $fields = $this->getDoctrine()
            ->getRepository('BachHomeBundle:BrowseFields')
            ->findBy(
                array('active' => true),
                array('position' => 'ASC')
            );

        $field = null;
        if ( $part === '' && count($fields) >0 ) {
            $field = $fields[0];
            $part = $field->getSolrFieldName();
        } else if ( count($fields) > 0 ) {
            foreach ( $fields as $f ) {
                if ( $f->getSolrFieldName() === $part ) {
                    $field = $f;
                    break;
                }
            }
        }

        $templateVars = array(
            'fields'        => $fields,
            'current_field' => $field,
            'part'          => $part
        );

        $lists = array();

        if ( $part !== '' ) {
            $client = $this->get($this->entryPoint());

            $query = $client->createSelect();
            $query->setQuery('*:*');
            $query->setRows(0);
            $facetSet = $query->getFacetSet();
            $facetSet->setLimit(-1);
            $facetSet->setMinCount(1);

            $facetSet->createFacetField($part)
                ->setField($part);

            $rs = $client->select($query);
            $facetSet = $rs->getFacetSet();
            $facets = $facetSet->getFacet($part);

            $lists[$part] = array();
            $current_values = array();
            foreach ( $facets as $term=>$count ) {
                $current_values[$term] = array(
                    'term'  => $term,
                    'count' => $count
                );
            }

            if ( defined('SORT_FLAG_CASE') ) {
                //TODO: find a better way!
                if ( $this->getRequest()->getLocale() == 'fr_FR' ) {
                    setlocale(LC_COLLATE, 'fr_FR.utf8');
                }
                ksort($current_values, SORT_LOCALE_STRING | SORT_FLAG_CASE);
            } else {
                //fallback for PHP < 5.4
                ksort($current_values, SORT_LOCALE_STRING);
            }

            if ( $part == 'headerId' ) {
                //retrieve documents titles...
                $ids = array();
                foreach ( $current_values as $v ) {
                    $ids[] = $v['term'] . '_description';
                }

                $query = $this->getDoctrine()->getManager()->createQuery(
                    'SELECT h.headerId, h.headerTitle ' .
                    'FROM BachIndexationBundle:EADFileFormat e ' .
                    'JOIN e.eadheader h WHERE e.fragmentid IN (:ids)'
                )->setParameter('ids', $ids);
                $lists[$part] = $query->getResult();
            } else {
                $lists[$part] = $current_values;
            }
        }

        $templateVars['lists'] = $lists;

        if ( $ajax === false ) {
            $tpl_name = 'browse';
        } else {
            $tpl_name = 'browse_tab_contents';
        }

        return $this->render(
            'BachHomeBundle:Default:' . $tpl_name  . '.html.twig',
            $templateVars
        );
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
        $with_context = true;

        if ( $this->getRequest()->get('nocontext') ) {
            $with_context = false;
        }

        $client = $this->get($this->entryPoint());
        $query = $client->createSelect();
        $query->setQuery('fragmentid:"' . $docid . '"');
        $query->setFields(
            'headerId, fragmentid, fragment, parents, ' .
            'archDescUnitTitle, cUnittitle, cDate, ' .
            'previous_id, previous_title, next_id, next_title'
        );
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

        $form_name = 'default';
        if ( $this->getRequest()->get('search_form') ) {
            $form_name = $this->getRequest()->get('search_form');
        }

        $tplParams = $this->commonTemplateVariables();
        $tplParams = array_merge(
            $tplParams,
            array(
                'docid'         => $docid,
                'document'      => $doc,
                'context'       => $with_context,
                'search_form'   => $form_name
            )
        );

        if ( isset($doc['archDescUnitTitle']) ) {
            $tplParams['archdesc'] = $doc['archDescUnitTitle'];
        }
        $parents = explode('/', $doc['parents']);
        if ( count($parents) > 0 ) {
            $pquery = $client->createSelect();
            $query = null;
            foreach ( $parents as $p ) {
                if ( $query !== null ) {
                    $query .= ' | ';
                }
                $query .= 'fragmentid:"' . $doc['headerId'] . '_' . $p . '"';
            }
            $pquery->setQuery($query);
            $pquery->setFields('fragmentid, cUnittitle');
            $rs = $client->select($pquery);
            $ariane  = $rs->getDocuments();
            if ( count($ariane) > 0 ) {
                $tplParams['ariane'] = $ariane;
            }
        }

        $max_results = 20;
        $cquery = $client->createSelect();
        $pid = substr($docid, strlen($doc['headerId']) + 1);

        $query = '+headerId:"' . $doc['headerId'] . '" +parents: ';
        if ( $pid === 'description' ) {
            $query .= '""';
        } else {
            if ( isset($doc['parents']) && trim($doc['parents'] !== '') ) {
                $pid = $doc['parents'] . '/' . $pid;
            }
            $query .= $pid;
        }
        $cquery->setQuery($query);
        $cquery->setStart(($page - 1) * $max_results);
        $cquery->setRows($max_results);
        $cquery->setFields('fragmentid, cUnittitle');
        $rs = $client->select($cquery);
        $children  = $rs->getDocuments();
        $count_children = $rs->getNumFound();

        $tplParams['count_children'] = $count_children;

        if ( count($children) > 0 ) {
            $tplParams['children'] = $children;
            if ( count($children) < $count_children ) {
                $tplParams['totalPages'] = ceil($count_children/$max_results);
                $tplParams['page'] = $page;
            }
        } else {
            $tplParams['children'] = false;
        }

        if ( $ajax === 'ajax' ) {
            $tpl = 'BachHomeBundle:Default:content_display.html.twig';
            $tplParams['ajax'] = true;
        } else {
            $tpl = 'BachHomeBundle:Default:display.html.twig';
            $tplParams['ajax'] = false;
        }

        //retrieve comments
        $show_comments = $this->container->getParameter('feature.comments');
        if ( $show_comments ) {
            $query = $this->getDoctrine()->getManager()
                ->createQuery(
                    'SELECT c, d FROM BachHomeBundle:Comment c
                    JOIN c.eadfile d
                    WHERE c.state = :state
                    AND d.fragmentid = :docid
                    ORDER BY c.creation_date DESC, c.id DESC'
                )->setParameters(
                    array(
                        'state' => Comment::PUBLISHED,
                        'docid' => $docid
                    )
                );
            $comments = $query->getResult();
            if ( count($comments) > 0 ) {
                $tplParams['comments'] = $comments;
            }
        }

        //check if HTML export do exist
        $html_export = $this->container->getParameter('bach_files_html') .
            '/' . $doc['headerId'] . '.html';
        $tplParams['html_export'] = file_exists($html_export);

        return $this->render(
            $tpl,
            $tplParams
        );
    }

    /**
     * Display classification scheme
     *
     * @return void
     */
    public function cdcAction()
    {
        $tplParams = array();

        $client = $this->get($this->entryPoint());
        $query = $client->createSelect();
        $query->setQuery('fragmentid:*_description');
        $query->setFields('cUnittitle, headerId, fragmentid');
        $query->setStart(0)->setRows(1000);

        $rs = $client->select($query);

        $published = new \SimpleXMLElement(
            '<docs></docs>'
        );

        foreach ( $rs as $doc ) {
            $published->addChild($doc->headerId, $doc->cUnittitle);
        }

        $tplParams['docs'] = $published;

        return $this->render(
            'BachHomeBundle:Default:cdc.html.twig',
            $tplParams
        );
    }

    /**
     * Displays an EAD document as HTML
     *
     * @param string  $docid    Document id
     * @param boolean $expanded Expand tree on load
     *
     * @return void
     */
    public function eadHtmlAction($docid, $expanded = false)
    {
        $tpl_vars = $this->commonTemplateVariables();

        $repo = $this->getDoctrine()
            ->getRepository('BachIndexationBundle:Document');
        $document = $repo->findOneByDocid($docid);

        if ( $document === null ) {
            throw new NotFoundHttpException(
                str_replace(
                    '%docid',
                    $docid,
                    _('Document "%docid" does not exists.')
                )
            );
        } else {
            if ( $document->isUploaded() ) {
                $document->setUploadDir(
                    $this->container->getParameter('upload_dir')
                );
            } else {
                $document->setStoreDir(
                    $this->container->getParameter('bach.typespaths')['ead']
                );
            }
            $xml_file = $document->getAbsolutePath();

            if ( !file_exists($xml_file) ) {
                throw new NotFoundHttpException(
                    str_replace(
                        '%docid',
                        $docid,
                        _('Corresponding file for %docid document no longer exists on disk.')
                    )
                );
            } else {
                $tpl_vars['docid'] = $docid;
                $tpl_vars['xml_file'] = $xml_file;
                $tpl_vars['expanded'] = ($expanded !== false);

                $form = $this->createForm(
                    new SearchQueryFormType(),
                    new SearchQuery()
                );
                $tpl_vars['form'] = $form->createView();

                return $this->render(
                    'BachHomeBundle:Default:html.html.twig',
                    $tpl_vars
                );
            }
        }
    }

    /**
     * Get available ordering options
     *
     * @return array
     */
    protected function getOrders()
    {
        $orders = array(
            _('Alphabetic'),
            _('Document logic')
        );
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
                'title' => _('View search results as a list, with images')
            ),
            'txtlist'   => array(
                'text'  => _('Text only list'),
                'title' => _('View search results as a list, without images')
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
        $form_name = 'main';
        if ( $this->search_form !== null ) {
            $form_name = $this->search_form;
        }

        return $this->getDoctrine()
            ->getRepository('BachHomeBundle:Facets')
            ->findBy(
                array(
                    'active'            => true,
                    'solr_field_name'   => $name,
                    'form'              => $form_name
                )
            );
    }

    /**
     * Get container field name
     *
     * @return string
     */
    protected function getContainerFieldName()
    {
        return 'main';
    }

    /**
     * Get filters session name
     *
     * @return string
     */
    protected function getFiltersName()
    {
        $name = 'filters';
        if ( $this->search_form !== null ) {
            $name .= '_form_' . $this->search_form;
        }
        return $name;
    }


    /**
     * Get search URI
     *
     * @return string
     */
    protected function getSearchUri()
    {
        return 'bach_search';
    }

    /**
     * Get view params service name
     *
     * @return string
     */
    protected function getViewParamsServicename()
    {
        return ('bach.home.ead_view_params');
    }

    /**
     * Serve twig generated CSS
     *
     * @param string $name CSS name
     *
     * @return void
     */
    public function dynamicCssAction($name)
    {
        return $this->render(
            '::' . $name . '.css.twig'
        );
    }

    /**
     * Get session name for view parameters
     *
     * @return string
     */
    protected function getParamSessionName()
    {
        $name = 'view_params';
        if ( $this->search_form !== null ) {
            $name .= '_form_' . $this->search_form;
        }
        return $name;
    }

    /**
     * Display HTML version of the document
     *
     * @param string $docid The document ID to load
     *
     * @return void
     */
    public function displayHtmlDocumentAction($docid)
    {
        $path = $this->container->getParameter('bach_files_html') .
            '/' . $docid . '.html';
        $html_contents = file_get_contents($path);

        return $this->render(
            'BachHomeBundle:Default:html_contents.html.twig',
            array(
                'html_contents' => $html_contents
            )
        );

    }

    /**
     * Display HTML sub-document
     *
     * @param string $maindir Main directory where resources are stored
     * @param string $file    The file to load
     * @param string $ext     File extension
     *
     * @return void
     */
    public function displayHtmlSubDocumentAction($maindir, $file, $ext)
    {
        $path = $this->container->getParameter('bach_files_html') .
            '/' . $maindir . '/' . $file . '.' . $ext;
        $html_contents = file_get_contents($path);

        $viewer_uri = $this->container->getParameter('viewer_uri');

        $callback = function ($matches) use ($viewer_uri) {
            $img_path = str_replace('../', '', $matches[2]);
            $href = $viewer_uri . 'viewer/' . $img_path;
            $thumb_href = $viewer_uri . 'ajax/img/' . $img_path . '/format/medium';

            return '<a href="' . $href . '"><img' . $matches[1] . ' src="' .
                $thumb_href . '"' . $matches[3] . '/></a>';
        };


        $html_contents = preg_replace_callback(
            '@<img(.*)src="(.*)"(.+)/>@',
            $callback,
            $html_contents
        );

        return $this->render(
            'BachHomeBundle:Default:html_contents.html.twig',
            array(
                'html_contents' => $html_contents
            )
        );

    }

    /**
     * Display scripts/css/images for HTML version of the document
     *
     * @param string $maindir Main directory where resources are stored
     * @param string $file    The file to load
     * @param string $ext     File extension
     *
     * @return void
     */
    public function displayHtmlDocumentExtAction($maindir, $file, $ext)
    {
        $path = $this->container->getParameter('bach_files_html') .
            '/' . $maindir . '/' . $file . '.' . $ext;

        $mime = null;
        switch( $ext ) {
        case 'js':
            $mime = 'text/javascript';
            break;
        case 'css':
            $mime = 'text/css';
            break;
        default:
            $mime = mime_content_type($path);
            break;
        }

        $fp = fopen($path, 'rb');
        $contents = stream_get_contents($fp);
        fclose($fp);
        $headers = array(
            'Content-Type'      => $mime
        );

        return new Response($contents, 200, $headers);
    }

}
