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
    private static $_sounds_extensions = array('ogg', 'wav');
    private static $_videos_extensions = array('ogv', 'mp4', 'webm', 'mov');
    private static $_flash_sounds_extensions = array('mp3');
    private static $_flash_extensions = array('flv');

    const IMAGE = 0;
    const SERIES = 1;
    const SOUND = 2;
    const VIDEO = 3;
    const FLA_SOUND = 4;
    const FLASH = 5;
    const MISC = 6;

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
            return self::proceedDao($daos[0], null, $this->_viewer, $format);
        } else {
            $res = '';
            foreach ( $daos as $dao ) {
                $res .= self::proceedDao($dao, null, $this->_viewer, $format);
            }
            return $res;
        }
    }

    /**
     * Get DAOs as XML nodes (XSLT will display them as string otherwise)
     *
     * @param NodeSet $daogrps    Groups list
     * @param NodeSet $daos       Documents list
     * @param string  $viewer     Viewer URL
     * @param string  $format     Format to display, defaults to thumb
     * @param boolean $ajax       Is call came from ajax
     * @param boolean $standalone Is document standalone? Defaults to true.
     *
     * @return DOMElement
     */
    public static function displayDaos($daogrps, $daos, $viewer, $format = 'thumb',
        $ajax = false, $standalone = true
    ) {
        //start root element
        $res = '<div>';

        if ( count($daogrps) > 0 ) {
            $groups_results = array();
            foreach ( $daogrps as $dg ) {
                $xml_dg = simplexml_import_dom($dg);

                $gres = array(
                    'title'     => null,
                    'content'   => array()
                );

                if ( $xml_dg['title'] ) {
                    $gres['title'] = (string)$xml_dg['title'];
                }

                foreach ( $xml_dg->children() as $node_name => $xml_dao ) {
                    if ( $node_name === 'dao' || $node_name === 'daoloc' ) {
                        $dao = (string)$xml_dao['href'];
                        $daotitle = null;
                        if ( $xml_dao['title'] ) {
                            $daotitle = $xml_dao['title'];
                        }
                        $gres['content'][] = self::proceedDao(
                            $dao,
                            $daotitle,
                            $viewer,
                            $format,
                            $ajax
                        );
                    }
                }
                $groups_results[] = $gres;
            }

            foreach ( $groups_results as $group ) {
                $res .= '<section>';
                if ( $group['title'] !== null ) {
                    $res .= '<header><h4>' . $group['title'] . '</h4></header>';
                }

                $res .= '<ul>';
                foreach ( $group['content'] as $document ) {
                    $res .= $document;
                }
                $res .= '</ul>';
                $res .= '</section>';
            }
        }

        if ( count($daos) > 0 ) {
            $results = array(
                self::IMAGE     => array(),
                self::SERIES    => array(),
                self::VIDEO     => array(),
                self::FLASH     => array(),
                self::SOUND     => array(),
                self::FLA_SOUND => array(),
                self::MISC     => array()
            );

            foreach ( $daos as $d ) {
                $xml_dao = simplexml_import_dom($d);
                $dao = (string)$xml_dao['href'];
                $daotitle = null;
                if ( $xml_dao['title'] ) {
                    $daotitle = $xml_dao['title'];
                }
                $results[self::_getType($dao)][] = self::proceedDao(
                    $dao,
                    $daotitle,
                    $viewer,
                    $format
                );
            }

            if ( count($results[self::IMAGE]) > 0 ) {
                $res .= '<section id="images">';
                $res .= '<header><h4>' . _('Images') . '</h4></header>';
                foreach ( $results[self::IMAGE] as $image ) {
                    $res .= $image;
                }
                $res .= '</section>';
            }

            if ( count($results[self::SERIES]) > 0 ) {
                $res .= '<section id="series">';
                $res .= '<header><h4>' . _('Series') . '</h4></header>';
                foreach ( $results[self::SERIES] as $series ) {
                    $res .= $series;
                }
                $res .= '</section>';
            }

            if ( count($results[self::SOUND]) > 0 ) {
                $res .= '<section id="sounds">';
                $res .= '<header><h4>' . _('Sounds') . '</h4></header>';
                foreach ( $results[self::SOUND] as $sound ) {
                    $res .= $sound;
                }
                $res .= '</section>';
            }

            if ( count($results[self::FLA_SOUND]) > 0 ) {
                $res .= '<section id="sounds">';
                $res .= '<header><h4>' . _('Flash sounds') . '</h4></header>';
                $res .= '<ul>';
                foreach ( $results[self::FLA_SOUND] as $sound ) {
                    $res .= $sound;
                }
                $res .= '</ul>';
                $res .= '</section>';
            }

            if ( count($results[self::VIDEO]) > 0 ) {
                $res .= '<section id="videos">';
                $res .= '<header><h4>' . _('Videos') . '</h4></header>';
                foreach ( $results[self::VIDEO] as $video ) {
                    $res .= $video;
                }
                $res .= '</section>';
            }

            if ( count($results[self::FLASH]) > 0 ) {
                $res .= '<section id="flashvideos">';
                $res .= '<header><h4>' . _('Flash videos') . '</h4></header>';
                foreach ( $results[self::FLASH] as $flash ) {
                    $res .= $flash;
                }
                $res .= '</section>';
            }

            if ( count($results[self::MISC]) > 0 ) {
                $res .= '<section id="other">';
                $res .= '<header><h4>' . _('Miscellaneous documents') .
                    '</h4></header>';
                foreach ( $results[self::MISC] as $other ) {
                    $res .= $other;
                }
                $res .= '</section>';
            }
        }

        //end root element
        $res .= '</div>';

        $sxml = simplexml_load_string($res);
        $doc = dom_import_simplexml($sxml);
        return $doc;
    }

    /**
     * Get a DAO as XML nodes (XSLT will display them as string otherwise)
     *
     * @param string $dao    Document name
     * @param string $title  Document title
     * @param string $viewer Viewer URL
     * @param string $format Format to display, defaults to thumb
     *
     * @return DOMElement
     */
    public static function getDao($dao, $title, $viewer, $format = 'thumb')
    {
        $str = self::proceedDao($dao, $title, $viewer, $format);
        $sxml = simplexml_load_string($str);
        $doc = dom_import_simplexml($sxml);
        return $doc;
    }

    /**
     * Proceed daos
     *
     * @param string  $dao        Document name
     * @param string  $daotitle   Document title, if any
     * @param string  $viewer     Viewer URL
     * @param string  $format     Format to display
     * @param boolean $ajax       Does call came from ajax
     * @param boolean $standalone Is a standalone document, defaults to true
     *
     * @return string
     */
    public static function proceedDao($dao, $daotitle, $viewer, $format,
        $ajax = false, $standalone = true
    ) {
        $ret = null;

        if ( !(substr($viewer, -1) === '/') ) {
            $viewer .= '/';
        }

        $title = str_replace(
            '%name%',
            ($daotitle) ? $daotitle : $dao,
            _("Play '%name%'")
        );
        switch ( self::_getType($dao) ) {
        case self::SERIES:
            $ret = '<a href="' . $viewer . 'series/' . $dao . '">';
            $ret .= '<img src="' . $viewer . 'ajax/representative/' .
                rtrim($dao, '/') .  '/format/' . $format  . '" alt="' . $dao . '"/>';
            if ( $daotitle !== null ) {
                $ret .= '<span class="title">' . $daotitle . '</span>';
            }
            $ret .= '</a>';
            break;
        case self::IMAGE:
            $ret = '<a href="' . $viewer . 'viewer/' . $dao . '">';
            $ret .= '<img src="' . $viewer . 'ajax/img/' . $dao .
                '/format/' . $format . '" alt="' . $dao .'"/>';
            if ( $daotitle !== null ) {
                $ret .= '<span class="title">' . $daotitle . '</span>';
            }
            $ret .= '</a>';
            break;
        case self::SOUND:
            $href = '/file/music/' . $dao;
            $ret .= '<audio controls="controls" width="300" height="30">';
            $ret .= '<source src="' . $href  . '"/>';
            $ret .= '</audio>';
            break;
        case self::VIDEO:
            $href = '/file/video/' . $dao;
            $ret = '<div class="htmlplayer standalone">';
            $ret .= '<video controls="controls" width="300" height="300">';
            $ret .= '<source src="' . $href  . '"/>';
            $ret .= '<a href="' . $href . '">' . _('Your browser does not support this video format, you may want to download file and watch it offline') . '</a>';
            $ret .= '</video>';
            if ( $daotitle !== null ) {
                $ret .= '<span class="title">' . $daotitle . '</span>';
            }
            $ret .= '</div>';
            break;
        case self::FLASH:
            $href = '/file/video/' . $dao;
            $class = '';
            if ( $standalone === true ) {
                $class = ' class="';
                if ( $ajax !== false ) {
                    $class .= 'ajaxflashplayer ';
                }
                $class .= 'flashplayer"';
            }
            $ret = '<a' . $class . ' href="' . $href . '" title="' .
                $title  . '">';
            if ( $standalone === true ) {
                $ret .= '<img src="/img/play_large.png" alt="' . $dao . '"/>';
            }
            if ( $daotitle !== null ) {
                $ret .= '<span class="title">' . $daotitle . '</span>';
            }
            $ret .= '</a>';
            break;
        case self::FLA_SOUND;
            $class = '';
            if ( $standalone === true ) {
                $class = ' class="';
                if ( $ajax !== false ) {
                    $class .= 'ajaxflashmusicplayer ';
                }
                $class .= 'flashmusicplayer"';
            }
            $href = '/file/music/' . $dao;
            $ret = '<li><a' . $class . ' href="' . $href . '" title="' .
                $title  . '">';
            if ( $daotitle !== null ) {
                $ret .= '<span class="title">' . $daotitle . '</span>';
            }
            $ret .= '</a></li>';
            break;
        case self::MISC:
            $title = str_replace(
                '%name%',
                $dao,
                _("Display '%name%'")
            );

            $href = '/file/misc/' . $dao;
            $ret = '<a href="' . $href . '" title="' . $title  . '">';
            if ( $daotitle ) {
                $ret .= '<span class="title">' . $daotitle . '</span>';
            }
            $ret .= '</a>';
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
        $fla_reg = "/^(.+)\.(" . implode('|', self::$_flash_extensions) . ")$/i";
        $snd_reg = "/^(.+)\.(" . implode('|', self::$_sounds_extensions) . ")$/i";
        $fla_snd_reg = "/^(.+)\.(" . implode(
            '|',
            self::$_flash_sounds_extensions
        ) . ")$/i";

        $type = null;
        if ( preg_match($fla_reg, $dao, $matches) ) {
            //document is a flahs video
            $type = self::FLASH;
        } else if ( preg_match($vid_reg, $dao, $matches) ) {
            //document is a video
            $type = self::VIDEO;
        } else if ( preg_match($snd_reg, $dao, $matches) ) {
            //document is a HTML5 sound
            $type = self::SOUND;
        } else if ( preg_match($fla_snd_reg, $dao, $matches) ) {
            //document is a flash sound
            $type = self::FLA_SOUND;
        } else if ( preg_match($img_reg, $dao, $matches) ) {
            //document is an image
            $type = self::IMAGE;
        } else if ( preg_match($all_reg, $dao, $matches) ) {
            //document is a unkonwn file
            $type = self::MISC;
        } else {
            //document should be a series
            $type = self::SERIES;
        }
        return $type;
    }

    /**
     * Get link to document
     *
     * @param string $href Link
     *
     * @return string
     */
    public static function getDocumentLink($href)
    {
        $href = '/file/misc/' . $href;
        return $href;
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
