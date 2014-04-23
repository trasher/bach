<?php
/**
 * Twig extension to display social buttons
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
 * Twig extension to display social buttons
 *
 * PHP version 5
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class BachSocialBar extends \Twig_Extension
{

    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container Container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'bach_social_bar';
    }

    /**
     * Get provided functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'socialButtons' => new \Twig_Function_Method(
                $this,
                'getSocialButtons',
                array('is_safe' => array('html'))
            ),
            'facebookButton' => new \Twig_Function_Method(
                $this,
                'getFacebookLikeButton',
                array('is_safe' => array('html'))
            ),
            'twitterButton' => new \Twig_Function_Method(
                $this,
                'getTwitterButton',
                array('is_safe' => array('html'))
            ),
            'googlePlusButton' => new \Twig_Function_Method(
                $this,
                'getGooglePlusButton',
                array('is_safe' => array('html'))
            ),
        );
    }

    /**
     * Get social buttons
     *
     * @param array $parameters Buttons parameters
     *
     * @return string
     */
    public function getSocialButtons($parameters = array())
    {
        // no parameters were defined, keeps default values
        if ( !array_key_exists('facebook', $parameters) ) {
            $render_parameters['facebook'] = array();
            // parameters are defined, overrides default values
        } else if ( is_array($parameters['facebook']) ) {
            $render_parameters['facebook'] = $parameters['facebook'];
            // the button is not displayed 
        } else {
            $render_parameters['facebook'] = false;
        }

        if ( !array_key_exists('twitter', $parameters) ) {
            $render_parameters['twitter'] = array();
        } else if ( is_array($parameters['twitter']) ) {
            $render_parameters['twitter'] = $parameters['twitter'];
        } else {
            $render_parameters['twitter'] = false;
        }

        if ( !array_key_exists('googleplus', $parameters) ) {
            $render_parameters['googleplus'] = array();
        } else if ( is_array($parameters['googleplus']) ) {
            $render_parameters['googleplus'] = $parameters['googleplus'];
        } else {
            $render_parameters['googleplus'] = false;
        }

        // get the helper service and display the template
        return $this->container->get('bach.socialBarHelper')
            ->socialButtons($render_parameters);
    }

    /**
     * Facebook like button
     *
     * @param array $parameters Fb like parameters
     *
     * @see https://developers.facebook.com/docs/reference/plugins/like/
     *
     * @return string
     */
    public function getFacebookLikeButton($parameters = array())
    {
        $enabled = $this->container->getParameter('social.fb');

        if ( !$enabled ) {
            return '';
        }

        // default values, you can override the values by setting them
        $parameters = $parameters + array(
            'url' => null,
            'locale'    => $this->container->getParameter('social.fb.locale'),
            'width'     => $this->container->getParameter('social.fb.width'),
            'showFaces' => $this->container->getParameter('social.fb.showFaces'),
            'layout'    => $this->container->getParameter('social.fb.layout'),
            'share'     => $this->container->getParameter('social.fb.share')
        );

        return $this->container->get('bach.socialBarHelper')
            ->facebookButton($parameters);
    }

    /**
     * Twitter buttons
     *
     * @param array $parameters Twitter parameters
     *
     * @return string
     */
    public function getTwitterButton($parameters = array())
    {
        $enabled = $this->container->getParameter('social.twitter');

        if ( !$enabled ) {
            return '';
        }

        $parameters = $parameters + array(
            'url'       => null,
            'locale'    => $this->container->getParameter('social.twitter.locale'),
            'message'   => $this->container->getParameter('social.twitter.message'),
            'text'      => $this->container->getParameter('social.twitter.text'),
            'via'       => $this->container->getParameter('social.twitter.via'),
            'tag'       => $this->container->getParameter('social.twitter.tag'),
        );

        return $this->container->get('bach.socialBarHelper')
            ->twitterButton($parameters);
    }

    /**
     * Google Plus button
     *
     * @param array $parameters G+ parameters
     *
     * @return string
     */
    public function getGooglePlusButton($parameters = array())
    {
        $enabled = $this->container->getParameter('social.gplus');

        if ( !$enabled ) {
            return '';
        }

        $parameters = $parameters + array(
            'url'           => null,
            'locale'        => $this->container->getParameter('social.gplus.locale'),
            'size'          => $this->container->getParameter('social.gplus.size'),
            'annotation'    => $this->container->getParameter('social.gplus.annotation'),
            'width'         => $this->container->getParameter('social.gplus.width'),
        );

        return $this->container->get('bach.socialBarHelper')
            ->googlePlusButton($parameters);
    }
}
