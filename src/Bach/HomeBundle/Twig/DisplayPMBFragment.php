<?php
/**
 * Twig extension to display an PMB fragment
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
 * @category Templating
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Twig extension to display an PMB fragment
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class DisplayPMBFragment extends \Twig_Extension
{
private $_router;
    private $_request;
    private $_viewer_uri;
    private $_covers_dir;
    private $_comms;

    /**
     * Main constructor
     *
     * @param UrlGeneratorInterface $router Router
     * @param boolean               $comms  Comments feature enabled
     */
    public function __construct(Router $router, $comms)
    {
        $this->_router = $router;
        $this->_comms = $comms;
    }

    /**
     * Set Request
     *
     * @param Request $request The Request
     *
     * @return void
     */
    public function setRequest(Request $request = null)
    {
        $this->_request = $request;
    }

    /**
     * Set viewer URI
     *
     * @param string $viewer_uri Viewer URL
     *
     * @return void
     */
    public function setViewer($viewer_uri)
    {
        $this->_viewer_uri = $viewer_uri;
    }

    /**
     * Set covers directory
     *
     * @param string $dir Covers directory
     *
     * @return void
     */
    public function setCoversDir($dir)
    {
        $this->_covers_dir = $dir;
    }

    /**
     * Get provided functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'displayFragment' => new \Twig_Function_Method($this, 'display')
        );
    }
    
    /**
     * Displays PMB fragment in HTML with XSLT
     *
     * @param string  $fragment    pmb fragment as XML
     * @param string  $docid       Document unique identifier
     * @param boolean $full        Displays full fragment, default to false
     * @param boolean $hasChildren Document has children
     * @param boolean $hasComments Document has comments
     * @param int     $countSub    Sub units count
     * @param boolean $ajax        Called from ajax
     *
     * @return string
     */
    public function display($fragment, $docid, $full = false, $hasChildren = false,
        $hasComments = false, $countSub = 0, $ajax = false
    ) {
        $proc = new \XsltProcessor();
        $proc->importStylesheet(
            simplexml_load_file(__DIR__ . '/pmb_display_fragment.xsl')
        );

        $router = $this->_router;
        $request = $this->_request;
        $callback = function ($matches) use ($router, $request) {
            $href = $router->generate(
                'bach_search',
                array(
                    'query_terms'   => $request->get('query_terms'),
                    'filter_field'  => 'c' . ucwords($matches[1]),
                    'filter_value'  => $matches[2]
                )
            );
            return 'href="' . str_replace('&', '&amp;', $href) . '"';
        };

        $xml = simplexml_load_string($fragment);
     
        /*$proc->setParameter('', 'full', $full);
        $proc->setParameter('', 'docid', $docid);
        $proc->setParameter('', 'viewer_uri', $this->_viewer_uri);
        $proc->setParameter('', 'covers_dir', $this->_covers_dir);
        $comments_enabled = $this->_comms ? 'true':'false';
        $proc->setParameter('', 'comments_enabled', $comments_enabled);
                if ( $hasChildren === true ) {
            $proc->setParameter('', 'children', 'true');
        }
        if ( $hasComments === true ) {
            $proc->setParameter('', 'comments', 'true');
        }
        if ( $countSub > 0 ) {
            $proc->setParameter('', 'count_subs', $countSub);
        }
        if ( $ajax === true ) {
            $proc->setParameter('', 'ajax', 'true');
        }*/

        $proc->registerPHPFunctions();
        $text = $proc->transformToXml($xml);


        //it is not possible to build routes from the XSL, so we'll build them here
        $text = preg_replace_callback(
            '/link="%%%(.[^:]+)::(.[^%]*)%%%"/',
            $callback,
            $text
        );

        $add_comment_path = $router->generate(
            'bach_add_comment',
            array('docid' => $docid)
        );
        return $text;
    }

    /**
     * Get translations from XSL stylesheet.
     * It would be possible to directly call _(),
     * but those strings would not be found with
     * standard gettext capabilities.
     *
     * @param string $ref String reference
     *
     * @return string
     */
    public static function i18nFromXsl($ref)
    {
        switch ( $ref ) {
        case 'Codage Unimarc:':
            return _('Codage Unimarc:');
            break;
        case 'Identifiant notice:':
            return _('Identifiant notice:');
            break;
        case 'Editor':
            return _('Editor');
            break;
        case 'Nom:':
            return _('Nom:');
            break;
        case 'City:':
            return _('City:');
            break;
        case 'Year:':
            return _('Year:');
            break;
        case 'Firstname:':
            return _('Firstname:');
            break;
        case 'Lastname:':
            return _('Lastname:');
            break;
        case 'Function:':
            return _('Function:');
            break;
        case 'Dates:':
            return _('Dates:');
            break;
        case 'WebSite:':
            return _('WebSite:');
            break;
        case 'Mention editor:':
            return _('Mention editor:');
            break;
        case 'f_411:':
            return _('f_411:');
            break;
        case 'Keywords:':
            return _('Keywords:');
            break;
        case 'Category:':
            return _('Category:');
            break;
        case '410:':
            return _('410:');
            break;
        case '225:':
            return _('225:');
            break;
        case 'Language:: ':
            return _('Language:');
            break;
        case 'Notes':
            return _('Notes');
            break;
        case 'Title:':
            return _('Title');
            break;
        case 'Primary Author':
            return _('Primary Author');
            break;
        case 'Primary Author collectivity':
            return _('Primary Author collectivity');
            break;
        case 'Secondary Author':
            return _('Secondary Author');
            break;
        case 'Secondary Author collectivity':
            return _('Secondary Author collectivity');
            break;
        case 'Other Author':
            return _('Other Author');
            break;
        case 'Decimal indexing':
            return _('Decimal indexing');
            break;
        case 'Decimal indexing':
            return _('Decimal indexing');
            break;
        case 'Zone Mother':
            return _('Zone Mother');
            break;
        case 'Note content':
            return _('Note content');
            break;
        case 'Note resume':
            return _('Note resume');
            break;
        case 'Note generale':
            return _('Note generale');
            break;
        default:
            //TODO: add an alert in logs, a translation may be missing!
            //Should we really throw an exception here?
            //return _($ref);
            throw new \RuntimeException(
                'Translation from XSL reference "' . $ref . '" is not known!'
            );
        }
    }

    /**
     * Extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'pmb_display_fragment';
    }

}