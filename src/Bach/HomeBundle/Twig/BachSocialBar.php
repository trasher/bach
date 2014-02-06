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
        // default values, you can override the values by setting them
        $parameters = $parameters + array(
            'url' => null,
            /*'locale' => 'en_US',*/
            'locale' => 'fr_FR',
            'send' => false,
            'width' => 300,
            'showFaces' => false,
            'layout' => 'button_count',
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
        $parameters = $parameters + array(
            'url' => null,
            /*'locale' => 'en',*/
            'locale' => 'fr',
            /*'message' => 'I want to share that page with you',*/
            'message' => 'Un document d\'archives intéressant pour vous : ',
            'text' => 'Tweet',
            /*'via' => 'The Acme team',*/
            'via' => 'anaphorelabs',
            'tag' => '#bach',
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
        $parameters = $parameters + array(
            'url' => null,
            /*'locale' => 'en',*/
            'locale' => 'fr',
            'size' => 'medium',
            'annotation' => 'bubble',
            'width' => '300',
        );

        return $this->container->get('bach.socialBarHelper')
            ->googlePlusButton($parameters);
    }
}
