<?php
/**
 * Twig extension to display an EAD document as HTML
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
use Doctrine\ORM\EntityManager;
use Bach\IndexationBundle\Entity\Document;
Use Symfony\Component\HttpFoundation\File\File;

/**
 * Twig extension to display an EAD document as HTML
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class DisplayHtml extends \Twig_Extension
{
    private $_router;
    private $_request;

    /**
     * Main constructor
     *
     * @param UrlGeneratorInterface $router Router
     */
    public function __construct(Router $router)
    {
        $this->_router = $router;
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
     * Get provided functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'displayHtml' => new \Twig_Function_Method($this, 'display')
        );
    }

    /**
     * Displays an EAD document as HTML with XSLT
     *
     * @param string  $docid    Document id
     * @param string  $xml_file Document
     * @param boolean $expanded Expand tree on load
     *
     * @return string
     */
    public function display($docid, $xml_file, $expanded)
    {
        $cache = new \Doctrine\Common\Cache\ApcCache();
        $cached_doc = null;
        $cached_doc_date = $cache->fetch('html_date_' . $docid);

        $redo = true;
        if ( $cached_doc_date ) {
            //check if document is newer than cache
            $f = new File($xml_file);

            $change_date = new \DateTime();
            $last_file_change = $f->getMTime();
            $change_date->setTimestamp($last_file_change);

            if ( $cached_doc_date > $change_date ) {
                $redo = false;
            }
        }

        if ( !$redo ) {
            $cached_doc = $cache->fetch('html_' . $docid);
        }

        if ( !$cached_doc ) {
            $html = '';
            $proc = new \XsltProcessor();
            $proc->importStylesheet(
                simplexml_load_file(__DIR__ . '/display_html.xsl')
            );

            $proc->setParameter('', 'docid', $docid);
            if ( $expanded === true ) {
                $proc->setParameter('', 'expanded', 'true');
            }
            $proc->registerPHPFunctions();

            $xml_doc = new \DOMDocument();
            $xml_doc->load($xml_file);

            $html .= $proc->transformToXml($xml_doc);

            $router = $this->_router;
            $request = $this->_request;
            $callback = function ($matches) use ($router, $request) {
                $href = '';
                if ( count($matches) > 2 ) {
                    $href = $router->generate(
                        'bach_search',
                        array(
                            'query_terms'   => $request->get('query_terms'),
                            'filter_field'  => 'c' . ucwords($matches[1]),
                            'filter_value'  => $matches[2]
                        )
                    );
                } else {
                    $href = $router->generate(
                        'bach_display_document',
                        array(
                            'docid' => $matches[1]
                        )
                    );
                }
                return 'href="' . str_replace('&', '&amp;', $href) . '"';
            };

            $html = preg_replace_callback(
                '/link="%%%(.[^:]+)::(.[^%]*)%%%"/',
                $callback,
                $html
            );

            $html = preg_replace_callback(
                '/link="%%%(.[^%]+)%%%"/',
                $callback,
                $html
            );

            $cache->save('html_' . $docid, $html);
            $cache->save('html_date_' . $docid, new \DateTime());
        } else {
            $html = $cached_doc;
        }

        return $html;
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
        case 'Title statement':
            return _('Title statement');
            break;
        case 'Title proper:':
            return _('Title proper:');
            break;
        case 'Author:':
            return _('Author:');
            break;
        case 'Subtitle:':
            return _('Subtitle:');
            break;
        case 'Sponsor:':
            return _('Sponsor:');
            break;
        case 'Publication statement':
            return _('Publication statement');
            break;
        case 'Publisher:':
            return _('Publisher:');
            break;
        case 'Date:':
            return _('Date:');
            break;
        case 'Address:':
            return _('Address:');
            break;
        case 'Edition statement':
            return _('Edition statement');
            break;
        case 'Profile':
            return _('Profile');
            break;
        case 'Creation:':
            return _('Creation:');
            break;
        case 'Language:':
            return _('Language:');
            break;
        case 'Description rules:':
            return _('Description rules:');
            break;
        case 'Number:':
            return _('Number:');
            break;
        case 'Series statement':
            return _('Series statement');
            break;
        case 'Note statement':
            return _('Note statement');
            break;
        case 'Revision description':
            return _('Revision description');
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
        return 'display_html';
    }
}
