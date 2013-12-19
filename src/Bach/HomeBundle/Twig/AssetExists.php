<?php
/**
 * Does asset exists
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

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Does asset exists
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class AssetExists extends \Twig_Extension
{
    private $_kernel;

    /**
     * Main constructor
     *
     * @param KernelInterface $kernel Kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->_kernel = $kernel;
    }

    /**
     * Get provided functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'asset_exists' => new \Twig_Function_Method($this, 'assetExists')
        );
    }

    /**
     * Checks if asset exists
     *
     * @param string $path Path to asset file
     *
     * @return boolean
     */
    public function assetExists($path)
    {
        $webRoot = realpath($this->_kernel->getRootDir() . '/../web/');
        $toCheck = realpath($webRoot . $path);

        // check if the file exists
        if (!is_file($toCheck)) {
            return false;
        }

        // check if file is well contained in web/ directory (prevents ../ in paths)
        if (strncmp($webRoot, $toCheck, strlen($webRoot)) !== 0) {
            return false;
        }

        return true;
    }

    /**
     * Extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'asset_exists';
    }
}
