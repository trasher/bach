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
     * @param string $type File type
     * @param string $name File name
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

    /**
     * Get a cover
     *
     * @param string $name File name
     *
     * @return void
     */
    public function getCoverAction($name)
    {
        $path = $this->container->getParameter('covers_dir');
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

        $mime = mime_content_type($path);
        header('Cache-Control: public');
        header('Content-type: ' . $mime);

        $width = 300;
        $height = 300;

        list($owidth, $oheight) = getimagesize($path);
        if ( $owidth > 300 || $oheight > 300 ) {
            $ratio_orig = $owidth/$oheight;
            if ($width/$height > $ratio_orig) {
                $width = $height*$ratio_orig;
            } else {
                $height = $width/$ratio_orig;
            }

            $image_p = imagecreatetruecolor($width, $height);
            $image = imagecreatefromjpeg($path);
            imagecopyresampled(
                $image_p,
                $image,
                0,
                0,
                0,
                0,
                $width,
                $height,
                $owidth,
                $oheight
            );

            imagejpeg($image_p, null, 100);
        } else {
            $width = $owidth;
            $height = $oheight;
        }
    }

}
