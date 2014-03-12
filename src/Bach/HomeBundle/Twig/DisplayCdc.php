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
    public function display(\SimpleXMLElement $docs)
    {
        $text = '';
        $xml = simplexml_load_file($this->_cdc_uri);

        //display archdesc informations
        $archdesc = clone $xml->archdesc;
        unset($archdesc->dsc);

        $proc = new \XsltProcessor();
        $xsl = $proc->importStylesheet(
            simplexml_load_file(__DIR__ . '/display_fragment.xsl')
        );

        $proc->setParameter('', 'cdc', 'true');
        $proc->registerPHPFunctions();

        // trying to send $archdesc to transformation will in facts
        // send the whole XML document!
        $dom = new \DOMDocument();
        $dom->loadXML($archdesc->asXML());
        $text = '<div class="cdcarchdesc well">' . $proc->transformToXml($dom)
            . '</div>';

        //display classification scheme itself
        $proc = new \XsltProcessor();
        $xsl = $proc->importStylesheet(
            simplexml_load_file(__DIR__ . '/display_cdc.xsl')
        );

        $dadocs = $xml->addChild('dadocs');
        foreach ( $docs as $doc ) {
            $dadocs->addChild($doc->getName(), $doc);
        }

        //find not published documents
        $this->_setNotMatched($xml, $docs);

        $proc->registerPHPFunctions();
        $text .= '<div class="css-treeview">' . $proc->transformToXml($xml) . '</div>';

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
                    'bach_ead_html',
                    array(
                        'docid' => $matches[1]
                    )
                );
            }
            return 'href="' . str_replace('&', '&amp;', $href) . '"';
        };

        $text = preg_replace_callback(
            '/link="%%%(.[^:]+)::(.[^%]*)%%%"/',
            $callback,
            $text
        );

        $text = preg_replace_callback(
            '/link="%%%(.[^%]+)%%%"/',
            $callback,
            $text
        );

        return $text;
    }

    /**
     * Set documents not matched in classification shceme
     *
     * @param SimpleXMLElement $xml  XML classification shceme
     * @param SimpleXMLElement $docs Published documents
     *
     * @return void
     */
    private function _setNotMatched(\SimpleXMLElement $xml, \SimpleXMLElement $docs)
    {
        $alllinks = $xml->xpath('//*[@href]');
        $docs = (array)$docs;
        $docs_id = array_keys($docs);

        foreach ( $alllinks as $link ) {
            $href = preg_replace('/\\.[^.\\s]{3,4}$/', '', $link['href']);
            if ( in_array($href, $docs_id) ) {
                unset($docs[$href]);
            }
        }

        if ( count($docs) > 0 ) {
            $not_matched = $xml->addChild('not_matched');
            foreach ( $docs as $doc_id=>$doc_name ) {
                $not_matched->addChild($doc_id, $doc_name);
            }
        }
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
        case 'Not classified':
            return _('Not classified');
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
