<?php
/**
 * Social bar helper
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Templating
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
