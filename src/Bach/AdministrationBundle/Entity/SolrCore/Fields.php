<?php
/**
 * Bach solr fields
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\SolrCore;

/**
 * Bach solr fields
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Fields
{
    private $_luke_uri = '%core/admin/luke';
    private $_reader;

    /**
     * Constructor
     *
     * @param BachCoreAdminConfigReader $reader Config reader
     */
    public function __construct($reader = null)
    {
        $this->_reader = $reader;
    }

    /**
     * Get core fields
     *
     * @param string $core    Core name
     * @param array  $exclude Fields to exclude
     *
     * @return array
     */
    public function getFacetFields($core, $exclude = array())
    {
        $xml_str = $this->_send(
            $this->_reader->getCoresURL() . '/' . $core . '/admin/luke',
            $options = array(
                'numTerms' => 0
            )
        );

        $xml = simplexml_load_string($xml_str);

        $xpath = '//lst[@name="fields"]';
        $nl = $xml->xpath($xpath);

        $facet_fields = array();
        $known_fields = $nl[0];

        foreach ( $known_fields->lst as $field ) {
            $name = (string)$field['name'];
            if ( !in_array($name, $exclude) ) {
                $facet_fields[$name] = $this->getFieldLabel($name);
            }
        }

        return $facet_fields;
    }

    /**
     * Sends an HTTP query (POST method) to Solr and returns
     * result as a SolrCoreResponse object.
     *
     * @param string $url     HTTP URL
     * @param array  $options Request options
     *
     * @return string
     */
    private function _send($url, $options = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ( $options !== null && is_array($options) ) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
        }

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
                "\nSent options: " . print_r($options, true) .
                "\nCheck cores status for more informations."
            );
        }

        return $response;
    }

    /**
     * Retrieve localized label for field
     *
     * @param string $name Field name
     *
     * @return string
     */
    public function getFieldLabel($name)
    {
        switch ( $name ) {
        case 'archDescRepository':
            return _('Document repository');
            break;
        case 'archDescUnitDate':
            return _('Document date');
            break;
        case 'archDescUnitTitle':
            return _('Document title');
            break;
        case 'cCorpname':
            return _('Corporate name');
            break;
        case 'cGenreform':
            return _('Physical characteristic');
            break;
        case 'cGeogname':
            return _('Geographical name');
            break;
        case 'cPersname':
            return _('Personal name');
            break;
        case 'cSubject':
            return _('Subject');
            break;
        case 'cUnitid':
            return _('Unit ID');
            break;
        case 'cUnittitle':
            return _('Unit title');
            break;
        case 'descriptors':
            return ('Descriptors');
            break;
        case 'headerAuthor':
            return _('File description author');
            break;
        case 'headerId':
            return _('Document identifier');
            break;
        case 'headerPublisher':
            return _('Document publisher');
            break;
        default:
            /*case 'dyndescr_cCorpname_auteur':
            case 'dyndescr_cGenreform_liste-typedocAC':
            case 'dyndescr_cGeogname_batiment':
            case 'dyndescr_cGeogname_liste-commune':
            case 'dyndescr_cGeogname_liste-courdeau':
            case 'dyndescr_cGeogname_liste-coursdeau':
            case 'dyndescr_cGeogname_liste-departement':
            case 'dyndescr_cGeogname_liste-quartier':
            case 'dyndescr_cGeogname_liste-sectioncom':
            case 'dyndescr_cPersname_auteur':
            case 'dyndescr_cSubject_contexte-historique':
            case 'dyndescr_cSubject_liste-programme':
            case 'dyndescr_cSubject_liste-theme':*/
            if ( strpos($name, 'dyndescr_') === 0 ) {
                //TODO try to guess a name?
                return $this->guessDynamicFieldLabel($name);
            } else {
                //unknown field, return name as is.
                return $name;
            }
            break;
        }
    }

    /**
     * Guess dynamic field label
     *
     * @param string $name Field name
     *
     * @return string
     */
    public function guessDynamicFieldLabel($name)
    {
        $exploded = explode(
            '_',
            str_replace('dyndescr_', '', $name)
        );
        $field_label = $this->getFieldLabel($exploded[0]);
        $dynamic_name = str_replace('dyndescr_' . $exploded[0] . '_', '', $name);
        return $field_label . ' (' . $dynamic_name . ')';
    }
}
