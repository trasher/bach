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
use Bach\HomeBundle\Entity\ViewParams;
use Bach\HomeBundle\Entity\SearchQueryFormType;
use Bach\HomeBundle\Entity\SearchQuery;
use Bach\HomeBundle\Entity\Comment;
use Bach\HomeBundle\Entity\BrowseFields;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Bach\HomeBundle\Entity\Filters;
use Bach\HomeBundle\Entity\GeolocFields;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @return void
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        /** Manage view parameters */
        $view_params = $session->get('view_params');
        if ( !$view_params ) {
            $view_params = new ViewParams();
        }
        //take care of user view params
        if ( isset($_COOKIE['bach_view_params']) ) {
            $view_params->bindCookie('bach_view_params');
        }

        //set current view parameters according to request
        $view_params->bind($request);

        $tpl_vars = $this->searchTemplateVariables($view_params);

        $form = $this->createForm(
            new SearchQueryFormType(),
            new SearchQuery()
        );
        $tpl_vars['form'] = $form->createView();

        $factory = $this->get($this->factoryName());
        $factory->setDateField($this->date_field);

        $show_tagcloud = $this->container->getParameter('feature.tagcloud');
        if ( $show_tagcloud ) {
            $tagcloud = $factory->getTagCloud($this->getDoctrine()->getManager());

            if ( $tagcloud ) {
                $tpl_vars['tagcloud'] = $tagcloud;
            }
        }

        $this->handleGeoloc($factory, $tpl_vars);

        $slider_dates = $factory->getSliderDates(new Filters());
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
        return 'map_facets';
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
        $view_params = $session->get('view_params');
        if ( !$view_params ) {
            $view_params = new ViewParams();
        }
        //take care of user view params
        if ( isset($_COOKIE['bach_view_params']) ) {
            $view_params->bindCookie('bach_view_params');
        }

        //set current view parameters according to request
        $view_params->bind($request);

        //store new view parameters
        $session->set('view_params', $view_params);

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
            $redirectUrl = $this->get('router')->generate('bach_homepage');
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

        $conf_facets = $this->getDoctrine()
            ->getRepository('BachHomeBundle:Facets')
            ->findBy(
                array('active' => true),
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

        $slider_dates = $factory->getSliderDates($filters);
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
     * @return void
     */
    public function doSearchAction()
    {
        $query = new SearchQuery();
        $form = $this->createForm(new SearchQueryFormType(), $query);
        $redirectUrl = $this->get('router')->generate('bach_homepage');

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

                $redirectUrl = $this->get('router')->generate(
                    'bach_search',
                    $url_vars
                );
            }
        }
        return new RedirectResponse($redirectUrl);
    }

    /**
     * Browse contents
     *
     * @param string  $part     Part to browse
     * @param boolean $show_all Show all results
     * @param boolean $ajax     If we were called from ajax
     *
     * @return void
     */
    public function browseAction($part = '', $show_all = false, $ajax = false)
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

        $limit = 20;
        if ( $show_all === 'show_all' ) {
            $limit = -1;
            $templateVars['show_all'] = true;
        } else {
            $templateVars['show_all'] = 'false';
        }

        if ( $part !== '' ) {
            $client = $this->get($this->entryPoint());
            // get a terms query instance
            $query = $client->createTerms();

            $query->setLimit($limit);
            $query->setFields($part);

            $found_terms = $client->terms($query);
            foreach ( $found_terms as $field=>$terms ) {
                $lists[$field] = array();
                $current_values = array();
                foreach ( $terms as $term=>$count ) {
                    $current_values[$term] = array(
                        'term'  => $term,
                        'count' => $count
                    );
                }
                if ( $show_all === 'show_all' ) {
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
                }
                if ( $field == 'headerId' ) {
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
                    $lists[$field] = $query->getResult();
                } else {
                    $lists[$field] = $current_values;
                }
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
            'archDescUnitTitle, cUnittitle, cDate'
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

        $tplParams = $this->commonTemplateVariables();
        $tplParams = array_merge(
            $tplParams,
            array(
                'docid'         => $docid,
                'document'      => $doc,
                'context'       => $with_context
            )
        );

        if ( $with_context ) {
            $tplParams['archdesc'] = $doc['archDescUnitTitle'];
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
        } else {
            $tplParams['count_children'] = 0;
        }

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

        /** FIXME: find a suitable comportement for the stuff to avoid loops
        $referer = $this->getRequest()->headers->get('referer');
        if ( $referer !== null ) {
            $tplParams['referer'] = $referer;
        }*/

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
        return $this->getDoctrine()
            ->getRepository('BachHomeBundle:Facets')
            ->findBy(
                array(
                    'active'            => true,
                    'solr_field_name'   => $name
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
        return 'filters';
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
}
