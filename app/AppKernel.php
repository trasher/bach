<?php

/**
 * Bach Kernel
 *
 * PHP version 5
 *
 * @category Core
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Bach Kernel
 *
 * @category Core
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class AppKernel extends Kernel
{
    /**
     * Returns an array of bundles to registers.
     *
     * @return BundleInterface[] An array of bundle instances.
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Nelmio\SolariumBundle\NelmioSolariumBundle(),
            new Lsw\GettextTranslationBundle\LswGettextTranslationBundle(),
            new Bach\IndexationBundle\BachIndexationBundle(),
            new Bach\HomeBundle\BachHomeBundle(),
            new Bach\AdministrationBundle\AdministrationBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Fp\OpenIdBundle\FpOpenIdBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new atoum\AtoumBundle\AtoumAtoumBundle();
            $bundles[] = new Jns\Bundle\XhprofBundle\JnsXhprofBundle();
        }

        return $bundles;
    }

    /**
     * Loads the container configuration
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     *
     * @return void
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(
            __DIR__ . '/config/config_' . $this->getEnvironment() . '.yml'
        );
    }

    /**
     * Gets the cache directory.
     *
     * @return string The cache directory
     */
    public function getCacheDir()
    {
        if ( defined('BACH_CACHE_DIR') ) {
            return BACH_CACHE_DIR;
        } else {
            return parent::getCacheDir();
        }
    }

    /**
     * Gets the log directory.
     *
     * @return string The log directory
     */
    public function getLogDir()
    {
        if ( defined('BACH_LOG_DIR') ) {
            return BACH_LOG_DIR;
        } else {
            return parent::getLogDir();
        }
    }
}
