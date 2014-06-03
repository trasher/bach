<?php
/**
 * Twig extension to display an EAD fragment
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
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Twig extension to display an EAD fragment
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class DisplayEADFragment extends \Twig_Extension
{
    private $_router;
    private $_request;
    private $_viewer_uri;
    private $_covers_dir;
    private $_comms;
    private $_cote_location;

    /**
     * Main constructor
     *
     * @param UrlGeneratorInterface $router   Router
     * @param boolean               $comms    Comments feature enabled
     * @param string                $cote_loc Cote location
     */
    public function __construct(Router $router, $comms, $cote_loc)
    {
        $this->_router = $router;
        $this->_comms = $comms;
        $this->_cote_location = $cote_loc;
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
     * Displays EAD fragment in HTML with XSLT
     *
     * @param string  $fragment    EAD fragment as XML
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
            simplexml_load_file(__DIR__ . '/display_fragment.xsl')
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
        $proc->setParameter('', 'full', $full);
        $proc->setParameter('', 'docid', $docid);
        $proc->setParameter('', 'viewer_uri', $this->_viewer_uri);
        $proc->setParameter('', 'covers_dir', $this->_covers_dir);
        $proc->setParameter('', 'cote_location', $this->_cote_location);
        $comments_enabled = $this->_comms ? 'true' : 'false';
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
        }
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

        $text = str_replace(
            '__path_add_comment__',
            $add_comment_path,
            $text
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
        case 'Publication informations':
            return _('Publication informations');
            break;
        case 'Physical description':
            return _('Physical description');
            break;
        case 'Descriptors':
            return _('Descriptors');
            break;
        case 'Gender:':
            return _('Gender:');
            break;
        case 'Extent:':
            return _('Extent:');
            break;
        case 'Dimensions:':
            return _('Dimensions:');
            break;
        case 'Appearance:':
            return _('Appearance:');
            break;
        case 'Title:':
            return _('Title:');
            break;
        case 'corpname:':
            return _('Corporate name:');
            break;
        case 'geogname:':
            return _('Geographical name:');
            break;
        case 'subject:':
            return _('Subject:');
            break;
        case 'persname:':
            return _('Personal name:');
            break;
        case 'function:':
            return _('Function:');
            break;
        case 'name:':
            return _('Name:');
            break;
        case 'genreform:':
            return _('Genre:');
            break;
        case 'Relative documents':
            return _('Relative documents');
            break;
        case 'Description:':
            return _('Description:');
            break;
        case 'Conservation history:':
            return _('Conservation history:');
            break;
        case 'Arrangement:':
            return _('Arrangement:');
            break;
        case 'Related material:':
            return _('Related material:');
            break;
        case 'Bibliography:':
            return _('Bibliography:');
            break;
        case 'Biography or history:':
            return _('Biography or history:');
            break;
        case 'Acquisition information:':
            return _('Acquisition information:');
            break;
        case 'Separated material:':
            return _('Separated material:');
            break;
        case 'Untitled unit':
            return _('Untitled unit');
            break;
        case 'Content':
            return _('Content');
            break;
        case 'Documents':
            return _('Documents');
            break;
        case 'Sub-units':
            return _('Sub-units');
            break;
        case 'Add comment':
            return _('Add comment');
            break;
        case 'Repository':
            return _('Repository:');
            break;
        case 'Language:':
            return _('Language:');
            break;
        case 'Comments':
            return _('Comments');
            break;
        case 'Bibliographic informations':
            return _('Bibliographic informations');
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
        return 'display_ead_fragment';
    }
}
