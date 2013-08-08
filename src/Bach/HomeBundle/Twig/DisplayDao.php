<?php
/**
 * Twig extension to display numeric documents
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
 * Twig extension to display numeric documents
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DisplayDao extends \Twig_Extension
{
    private $_router;
    private $_viewer;

    private static $_images_extensions = array('jpeg', 'jpg', 'png', 'gif');
    private static $_sounds_extensions = array('mp3', 'ogg');
    private static $_videos_extensions = array('flv', 'ogv', 'mp4', 'webm');

    const IMAGE = 0;
    const SERIES = 1;
    const SOUND = 2;
    const VIDEO = 3;
    const OTHER = 4;

    /**
     * Main constructor
     *
     * @param UrlGeneratorInterface $router     Router
     * @param string                $viewer_uri Viewer URI
     */
    public function __construct(Router $router, $viewer_uri)
    {
        $this->_router = $router;
        if ( !(substr($viewer_uri, -1) === '/') ) {
            $viewer_uri .= '/';
        }
        $this->_viewer = $viewer_uri;
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
            'displayDao' => new \Twig_Function_Method($this, 'display')
        );
    }

    /**
     * Displays numeric documents regarding to their type
     *
     * @param string  $daos   Documents
     * @param boolean $all    Displays all documents, default to false
     * @param string  $format Format to display, defaults to thumb
     *
     * @return string
     */
    public function display($daos, $all = false, $format = 'thumb')
    {
        if ( $all === false ) {
            return self::proceedDao($daos[0], $this->_viewer, $format);
        } else {
            $res = '';
            foreach ( $daos as $dao ) {
                $res .= self::proceedDao($dao, $this->_viewer, $format);
            }
            return $res;
        }
    }

    /**
     * Get DAOs as XML nodes (XSLT will display them as string otherwise)
     *
     * @param NodeSet $daos   Documents list
     * @param string  $viewer Viewer URL
     * @param string  $format Format to display, defaults to thumb
     *
     * @return DOMElement
     */
    public static function displayDaos($daos, $viewer, $format = 'thumb')
    {
        $results = array(
            self::IMAGE     => array(),
            self::SERIES    => array(),
            self::VIDEO     => array(),
            self::SOUND     => array(),
            self::OTHER     => array()
        );

        foreach ( $daos as $d ) {
            $dao = (string)simplexml_import_dom($d)['href'];
            $results[self::_getType($dao)][] = self::proceedDao(
                $dao,
                $viewer,
                $format
            );
        }

        $res = '<div>';
        if ( count($results[self::IMAGE]) > 0 ) {
            $res .= '<h4>' . _('Images') . '</h4>';
            $res .= '<section id="images">';
            foreach ( $results[self::IMAGE] as $image ) {
                $res .= $image;
            }
            $res .= '</section>';
        }

        if ( count($results[self::SERIES]) > 0 ) {
            $res .= '<h4>' . _('Series') . '</h4>';
            $res .= '<section id="series">';
            foreach ( $results[self::SERIES] as $series ) {
                $res .= $series;
            }
            $res .= '</section>';
        }

        if ( count($results[self::SOUND]) > 0 ) {
            $res .= '<h4>' . _('Sounds') . '</h4>';
            $res .= '<section id="sounds">';
            foreach ( $results[self::SOUND] as $sound ) {
                $res .= $sound;
            }
            $res .= '</section>';
        }

        if ( count($results[self::VIDEO]) > 0 ) {
            $res .= '<h4>' . _('Videos') . '</h4>';
            $res .= '<section id="videos">';
            foreach ( $results[self::VIDEO] as $video ) {
                $res .= $video;
            }
            $res .= '</section>';
        }

        if ( count($results[self::OTHER]) > 0 ) {
            $res .= '<h4>' . _('Other documents') . '</h4>';
            $res .= '<section id="other">';
            foreach ( $results[self::OTHER] as $other ) {
                $res .= $other;
            }
            $res .= '</section>';
        }
        $res .= '</div>';

        $sxml = simplexml_load_string($res);
        $doc = dom_import_simplexml($sxml);
        return $doc;
    }

    /**
     * Get a DAO as XML nodes (XSLT will display them as string otherwise)
     *
     * @param string $dao    Document name
     * @param string $viewer Viewer URL
     * @param string $format Format to display, defaults to thumb
     *
     * @return DOMElement
     */
    public static function getDao($dao, $viewer, $format = 'thumb')
    {
        $str = self::proceedDao($dao, $viewer, $format);
        $sxml = simplexml_load_string($str);
        $doc = dom_import_simplexml($sxml);
        return $doc;
    }

    /**
     * Proceed daos
     *
     * @param string $dao    Document name
     * @param string $viewer Viewer URL
     * @param string $format Format to display
     *
     * @return string
     */
    public static function proceedDao($dao, $viewer, $format)
    {
        $ret = null;

        if ( !(substr($viewer, -1) === '/') ) {
            $viewer .= '/';
        }

        switch ( self::_getType($dao) ) {
        case self::SERIES:
            $ret = '<a href="' . $viewer . 'series/' . $dao . '">';
            $ret .= '<img src="' . $viewer . 'ajax/representative/' .
                rtrim($dao, '/') .  '/format/' . $format  . '" alt="' . $dao . '"/>';
            $ret .= '</a>';
            break;
        case self::IMAGE:
            $ret = '<a href="' . $viewer . 'viewer/' . $dao . '">';
            $ret .= '<img src="' . $viewer . 'ajax/img/' . $dao . 
                '/format/' . $format . '" alt="' . $dao .'"/>'; 
            $ret .= '</a>';
            break;
        case self::VIDEO:
            $ret = _('Videos are not supported (yet).');
            break;
        case self::SOUND;
            $ret = _('Sounds are not supported (yet).');
            break;
        case self::OTHER:
            $ret = _('Documents are not supported (yet).');
            break;
        }

        return $ret;
    }

    /**
     * Retrieve Dao type
     *
     * @param string $dao Document name
     *
     * @return int
     */
    private static function _getType($dao)
    {
        $all_reg = "/^(.+)\.(.+)$/i";
        $img_reg = "/^(.+)\.(" . implode('|', self::$_images_extensions) . ")$/i";
        $vid_reg = "/^(.+)\.(" . implode('|', self::$_videos_extensions) . ")$/i";
        $snd_reg = "/^(.+)\.(" . implode('|', self::$_sounds_extensions) . ")$/i";

        $type = null;
        if ( preg_match($vid_reg, $dao, $matches) ) {
            //document is a video
            $type = self::VIDEO;
        } else if ( preg_match($snd_reg, $dao, $matches) ) {
            //document is a sound
            $type = self::SOUND;
        } else if ( preg_match($img_reg, $dao, $matches) ) {
            //document is an image
            $type = self::IMAGE;
        } else if ( preg_match($all_reg, $dao, $matches) ) {
            //document is a file
            $type = self::OTHER;
        } else {
            //document should be a series
            $type = self::SERIES;
        }
        return $type;
    }

    /**
     * Extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'display_dao';
    }
}
