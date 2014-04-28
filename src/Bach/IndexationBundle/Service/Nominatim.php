<?php
/**
 * Nominatim query
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
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Service;

use Bach\IndexationBundle\Entity\Toponym;

/**
 * Nominatim query
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class Nominatim
{
    private $_query_options = array(
        'format'            => 'xml',
        'polygon_geojson'   => '1',
        'email'             => 'dev@anaphore.eu',
        'limit'             => '3',
        'addressdetails'    => '1'
    );
    private $_uri = "http://nominatim.openstreetmap.org/search";

    /**
     * Get results for a toponym
     *
     * @param Toponym $toponym Toponym
     * @param boolean $one     True if we want only one result back
     *
     * @return string
     */
    public function proceed(Toponym $toponym, $one = true)
    {
        $options = $this->_query_options;

        if ( $toponym->getCountry() !== null ) {
            $options['country'] = $toponym->getCountry();
        }

        if ( $toponym->getCounty() !== null ) {
            if ( $toponym->getCounty() !== 'Guyane' ) {
                $options['county'] = $toponym->getCounty();
            } else {
                //specific case for Guyane :/
                if ( isset($options['country']) ) {
                    unset($options['country']);
                }
                $options['state'] = $toponym->getCounty();
            }
        }

        if ( $toponym->getType() === Toponym::TYPE_TOWN ) {
            $options['city'] = $toponym->getName();
        } else if ( $toponym->getType() === Toponym::TYPE_STATE  ) {
            $options['state'] = $toponym->getName();
        } elseif ( $toponym->getType() === Toponym::TYPE_SPECIFIC ) {
            if ( $toponym->getName() !== '' ) {
                $options['city'] = $toponym->getName();
            }
            $options['q'] = $toponym->getSpecificName();
        }

        $result = $this->_send(
            $this->_uri,
            $options
        );

        $xml = new \SimpleXMLElement($result);

        $places = null;

        //try to find more relevant result
        if ( isset($options['city']) ) {
            //looking for a city. Address details <city> tag should be present.
            $places = $xml->xpath('//place[city]');
            if ( count($places) === 0 ) {
                $places = $xml->xpath('//place[town]');
            }
        } elseif ( isset($options['county']) ) {
            //looking for a county. Address details <county> tag should be present.
            $places = $xml->xpath('//place[county]');
        } elseif ( isset($options['state']) ) {
            $places = $xml->xpath('//place[state]');
        }

        if ( $places === null ) {
            $places = $xml->xpath('//place');
        }

        if ( count($places) > 1 ) {
            if ( $one === true ) {
                echo 'More than one place find for ' . $toponym->__toString() .
                    ", ignoring.\n";
                return false;
            } else {
                return $places;
            }
        } else if (count($places) == 0 ) {
            if ( $one === true ) {
                echo 'No result found for ' . $toponym->__toString() . " :(\n";
            }
            return false;
        } else {
            return $places[0];
        }

    }

    /**
     * Get results for a name
     *
     * @param String $name Name to search on
     *
     * @return string
     */
    public function rawProceed($name)
    {
        $options = $this->_query_options;
        $options['q'] = $name;

        $result = $this->_send(
            $this->_uri,
            $options
        );

        $xml = new \SimpleXMLElement($result);
        $places = $xml->xpath('//place');

        if (count($places) == 0 ) {
            return false;
        } else {
            return $places;
        }
    }


    /**
     * Sends an HTTP query (POST method) to Solr and returns
     * result.
     *
     * @param string $url     HTTP URL
     * @param array  $options Request options
     *
     * @return string
     */
    private function _send($url, $options)
    {
        if ( !is_array($options) ) {
            throw new \RuntimeException(
                'Options MUST be an array!'
            );
        }

        $url_options = array();
        foreach ( $options as $key=>$value ) {
            $url_options[] = $key . '=' . urlencode($value);
        }

        $url .= '?' . implode('&', $url_options);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        if ( $response === false ) {
            throw new \RuntimeException(
                "Error on request:\n\tURI:" . $url . "\n\toptions:\n" .
                print_r($options, true)
            );
        }

        //get request infos
        $infos = curl_getinfo($ch);
        if ( $infos['http_code'] !== 200 ) {
            $trace = debug_backtrace();
            $caller = $trace[1];

            //At this point, core has been created, but is failing 
            //to load in solr.
            throw new \RuntimeException(
                'Something went wrong in function ' . __CLASS__ . '::' .
                $caller['function'] . "\nHTTP Request URI: " . $url .
                "\nSent options: " . print_r($options, true)
            );
        }

        return $response;
    }
}
