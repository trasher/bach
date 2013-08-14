<?php
/**
 * Twig extension to display an EAD fragment
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

/**
 * Twig extension to display an EAD fragment
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DisplayEADFragment extends \Twig_Extension
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
            'displayFragment' => new \Twig_Function_Method($this, 'display')
        );
    }

    /**
     * Displays EAD fragment in HTML with XSLT
     *
     * @param string  $fragment    EAD fragment as XML
     * @param boolean $full        Displays full fragment, default to false
     * @param boolean $hasChildren Document has children
     *
     * @return string
     */
    public function display($fragment, $full = false, $hasChildren = false)
    {
        $proc = new \XsltProcessor();
        $xsl = $proc->importStylesheet(
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
            return 'href="' . $href . '"';
        };

        //FIXME: get that one from parameters :'(
        $viewer_uri = 'http://viewer.localhost';

        $xml = simplexml_load_string($fragment);
        $proc->setParameter('', 'full', $full);
        $proc->setParameter('', 'viewer_uri', $viewer_uri);
        if ( $hasChildren === true ) {
            $proc->setParameter('', 'children', 'true');
        }
        $proc->setParameter('', 'viewer_uri', $viewer_uri);
        $proc->registerPHPFunctions();
        $text = $proc->transformToXml($xml);
        //it is not possible to build routes from the XSL, so we'll build them here
        $text = preg_replace_callback(
            '/link="%%%(.[^:]+)::(.[^%]*)%%%"/',
            $callback,
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
            return ('Gender:');
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
            return ('Subject:');
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
        case 'Relative documents':
            return _('Relative documents');
            break;
        case 'Description:':
            return _('Description:');
            break;
        case 'Conservation history:':
            return _('Conservation history:');
            break;
        default:
            //FIXME: add an alert in logs, a translation may be missing!
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
