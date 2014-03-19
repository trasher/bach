<?php
/**
 * Twig extension to display an EAD document as HTML
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Bach\IndexationBundle\Entity\Document;

/**
 * Twig extension to display an EAD document as HTML
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
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
     * @param string      $docid    Document id
     * @param DOMDocument $xml_doc  Document
     * @param boolean     $expanded Expand tree on load
     *
     * @return string
     */
    public function display($docid, \DOMDocument $xml_doc, $expanded)
    {
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
