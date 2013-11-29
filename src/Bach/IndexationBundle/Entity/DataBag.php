<?php
/**
 * Data bag
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity;

/**
 * Data bag
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
abstract class DataBag
{
    protected $type;

    protected $data;

    protected $fileInfo;

    /**
     * Type Getter
     *
     * @return string The type of bag
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * fileInfo Getter
     *
     * @return SplFileInfo The spl object of the bag file
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * Data Getter
     *
     * @return mixed The content of bag
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Data Setter
     *
     * @param mixed $data The content of bag
     *
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
