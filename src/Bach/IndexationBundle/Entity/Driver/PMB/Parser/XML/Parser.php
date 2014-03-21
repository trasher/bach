<?php
/**
 * PMB Parser
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity\Driver\PMB\Parser\XML;

use Bach\IndexationBundle\Entity\ObjectTree;
use Bach\IndexationBundle\Entity\ObjectSheet;
use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\ParserInterface;

/**
 * PMB Parser
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Parser implements ParserInterface
{

    private $_tree;
    private $_configuration;

    /**
     * The constructor
     *
     * @param DataBag $bag           The bag of data
     * @param array   $configuration The caller driver configuration
     */
    public function __construct(DataBag $bag, $configuration)
    {
        $this->_configuration = $configuration;
        $this->_tree = new ObjectTree("root");
        $this->parse($bag);
    }

    /**
     * Parse the input data
     *
     * @param DataBag $bag The bag of data
     *
     * @return void
     */
    public function parse(DataBag $bag)
    {
        $dom = $bag->getData();
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//notice');
        $pmbs = array();
		foreach ($nodes as $node) {
	        $pmb = new PMB(
	            $xpath,
	            $node,
	            $this->_configuration['fields']
        	);
	        $id = $xpath->query('idNotice', $node)->item(0)->nodeValue;
	        $pmbs[] = $pmb;
		}
		$this->_tree->append(
			new ObjectSheet('notices', $pmbs)
		);
    }

    /**
     * Return the parser's ObjectTree
     *
     * @return ObjectTree The parser's tree
     */
    public function getTree()
    {
        return $this->_tree;
    }
}
