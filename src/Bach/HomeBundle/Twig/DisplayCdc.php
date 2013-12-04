<?php
/**
 * Twig extension to display a classification shceme from EAD
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
class DisplayCdc extends \Twig_Extension
{
    private $_router;
    private $_request;
    private $_cdc_uri;

    /**
     * Main constructor
     *
     * @param UrlGeneratorInterface $router Router
     * @param string                $path   Classification scheme URL
     */
    public function __construct(Router $router, $path)
    {
        $this->_router = $router;
        $this->_cdc_uri = $path;
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
            'displayCdc' => new \Twig_Function_Method($this, 'display')
        );
    }

    /**
     * Displays classification scheme from EAD in HTML with XSLT
     *
     * @param SimpleXMLElement $docs Published documents
     *
     * @return string
     */
    public function display($docs)
    {
        $proc = new \XsltProcessor();
        $xsl = $proc->importStylesheet(
            simplexml_load_file(__DIR__ . '/display_cdc.xsl')
        );

        $xml = simplexml_load_file($this->_cdc_uri);

        $dadocs = $xml->addChild('dadocs');
        foreach ( $docs as $doc ) {
            $dadocs->addChild($doc->getName(), $doc);
        }

        $proc->registerPHPFunctions();
        $text = $proc->transformToXml($xml);

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
                        'filter_value'  => $matches[3]
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

        $text = preg_replace_callback(
            '/link="%%%(.[^:]+)(::(.[^%]*))?%%%"/',
            $callback,
            $text
        );

        return $text;
    }

    /**
     * Does current node contains published docs?
     *
     * @param DOMElement[] $node Current node
     * @param DOMElement[] $docs Published documents
     *
     * @return boolean
     */
    public static function hasPublished($node, $docs)
    {
        $docs = array_keys((array)simplexml_import_dom($docs[0]));

        $simple_node = simplexml_import_dom($node[0]);
        $links = $simple_node->xpath('descendant::*[@href]');

        foreach ( $links as $link ) {
            $href = preg_replace('/\\.[^.\\s]{3,4}$/', '', $link['href']);
            if ( in_array($href, $docs) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'display_cdc';
    }
}
