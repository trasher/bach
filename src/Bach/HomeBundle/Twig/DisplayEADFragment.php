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
     * @param string  $fragment EAD fragment as XML
     * @param boolean $full     Displays full fragment, default to false
     *
     * @return string
     */
    public function display($fragment, $full = false)
    {
        $proc = new \XsltProcessor();
        $xsl = $proc->importStylesheet(
            simplexml_load_file(__DIR__ . '/display_fragment.xsl')
        );

        $xml = simplexml_load_string($fragment);
        return $proc->transformToXml($xml);
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
