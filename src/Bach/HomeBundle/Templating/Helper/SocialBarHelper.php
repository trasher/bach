<?php
/**
 * Social bar helper
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;

/**
 * Social bar helper
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SocialBarHelper extends Helper
{
    protected $templating;

    /**
     * Constructor
     *
     * @param EngineInterface $templating Templating instance
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating  = $templating;
    }

    /**
     * Get social buttons bar
     *
     * @param array $parameters Bar parameters
     *
     * @return string
     */
    public function socialButtons($parameters)
    {
        return $this->templating->render(
            'BachHomeBundle:helper:socialButtons.html.twig',
            $parameters
        );
    }

    /**
     * Get facebook button
     *
     * @param array $parameters Button parameters
     *
     * @return string
     */
    public function facebookButton($parameters)
    {
        return $this->templating->render(
            'BachHomeBundle:helper:facebookButton.html.twig',
            $parameters
        );
    }

    /**
     * Get twitter button
     *
     * @param array $parameters Button parameters
     *
     * @return string
     */
    public function twitterButton($parameters)
    {
        return $this->templating->render(
            'BachHomeBundle:helper:twitterButton.html.twig',
            $parameters
        );
    }

    /**
     * Get google plus button
     *
     * @param array $parameters Button parameters
     *
     * @return string
     */
    public function googlePlusButton($parameters)
    {
        return $this->templating->render(
            'BachHomeBundle:helper:googlePlusButton.html.twig',
            $parameters
        );
    }

    /**
     * Helper name
     *
     * @return string
     */
    public function getName()
    {
        return 'socialButtons';
    }
}
