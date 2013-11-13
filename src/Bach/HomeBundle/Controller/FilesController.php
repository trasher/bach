<?php
/**
 * Bach files controller
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Bach files controller
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class FilesController extends Controller
{
    /**
     * Get a file
     *
     * @param string  $type File type
     * @param boolean $name File name
     *
     * @return void
     */
    public function getFileAction($type, $name)
    {
        $path = null;
        switch ( $type ) {
        case 'video':
            if ( !defined('BACH_FILES_VIDEOS') ) {
                throw new \RuntimeException(
                    _('Videos path is not defined!')
                );
            } else {
                $path = BACH_FILES_VIDEOS;
            }
            break;
        case 'music':
            if ( !defined('BACH_FILES_MUSICS') ) {
                throw new \RuntimeException(
                    _('Musics path is not defined!')
                );
            } else {
                $path = BACH_FILES_MUSICS;
            }
            break;
        case 'misc':
            if ( !defined('BACH_FILES_MISC') ) {
                throw new \RuntimeException(
                    _('Misc files path is not defined!')
                );
            } else {
                $path = BACH_FILES_MISC;
            }
            break;
        }
        $path .= '/' . $name;

        if ( !file_exists($path) ) {
            throw new \RuntimeException(
                str_replace(
                    '%file',
                    $path,
                    _('File %file does not exists.')
                )
            );
        }

        $file = fopen($path, 'rb');
        $out = fopen('php://output', 'wb');

        $mime = mime_content_type($path);
        header('Cache-Control: public');
        header('Content-type: ' . $mime);
        header('Content-Length:' . filesize($path));
        stream_copy_to_stream($file, $out);

        fclose($out);
        fclose($file);
    }
}
