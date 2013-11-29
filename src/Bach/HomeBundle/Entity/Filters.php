<?php
/**
 * Bach search filters
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity;

/**
 * Bach search filters
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Filters extends \ArrayObject
{

    /**
     * Bind request
     *
     * @param Request $request Request to bind
     *
     * @return void
     */
    public function bind($request)
    {
        if ( $request->get('rm_filter_field') ) {
            $rm_filter_field = $request->get('rm_filter_field');
            $rm_filter_value = $request->get('rm_filter_value');

            $this->removeFilter($rm_filter_field, $rm_filter_value);
        }

        if ( $request->get('filter_field') ) {

            $filter_fields = $request->get('filter_field');
            $filter_values = $request->get('filter_value');

            if ( !is_array($filter_fields) ) {
                $filter_fields = array($filter_fields);
            }
            if ( !is_array($filter_values) ) {
                $filter_values = array($filter_values);
            }

            if ( count($filter_fields) != count($filter_values) ) {
                throw new \RuntimeException(
                    'Filter fields and values does not match!'
                );
            }

            for ( $i = 0; $i < count($filter_fields); $i++ ) {
                $this->addFilter(
                    $filter_fields[$i],
                    $filter_values[$i]
                );
            }
        }
    }

    /**
     * Add a filter
     *
     * @param string $field Filter field
     * @param string $value Filter value
     *
     * @return void
     */
    public function addFilter($field, $value)
    {
        switch ( $field ) {
        case 'cDateBegin':
        case 'cDateEnd':
            //only one start and end date allowed
            $php_date = \DateTime::createFromFormat('Y', $value);
            if ( $field === 'cDateBegin' ) {
                $value = $php_date->format('Y-01-01');
            } else {
                $value = $php_date->format('Y-12-31');
            }
            $this->offsetSet($field, $value);
            break;
        case 'cDate':
        case 'dao':
            //avoid mutliple values
            $this->offsetSet($field, $value);
            break;
        default:
            if ( !$this->offsetExists($field) ) {
                //initialize filter field if it does not exists
                $this->offsetSet(
                    $field,
                    new \ArrayObject(array($value))
                );
            } else if ( !$this->hasValue($field, $value) ) {
                //check if value already exists in current filters
                $this->offsetGet($field)->append($value);
            }
        }
    }

    /**
     * Remove a filter
     *
     * @param string $field Filter field
     * @param string $value Filter value
     *
     * @return void
     */
    public function removeFilter($field, $value)
    {
        switch ( $field ) {
        case 'cDateBegin':
        case 'cDateEnd':
        case 'cDate':
        case 'dao':
            $this->offsetUnset($field);
            break;
        default:
            if ( $this->offsetExists($field)
                && $this->hasValue($field, $value)
            ) {
                $offset = $this->offsetGet($field);
                if ( $offset->count() > 1 ) {
                    $iterator = $offset->getIterator();
                    while ( $iterator->valid() ) {
                        if ( $iterator->current() === $value ) {
                            $offset->offsetUnset($iterator->key());
                            break;
                        }
                        $iterator->next();
                    }
                } else {
                    //field contains only one value, unset
                    $this->offsetUnset($field);
                }
            }
        }
    }

    /**
     * Does field filter already contains value?
     *
     * @param string $field Fields name
     * @param string $value Filter value
     *
     * @return boolean
     */
    public function hasValue($field, $value)
    {
        return in_array(
            $value,
            (array)$this->offsetGet($field)
        );
    }
}
