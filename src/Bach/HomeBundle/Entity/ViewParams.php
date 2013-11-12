<?php
/**
 * Search view parameters
 *
 * PHP version 5
 *
 * @category Parameters
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity;

use Symfony\Component\HttpFoundation\Request;

/**
 * Search view parameters
 *
 * @category Parameters
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class ViewParams
{
    const ORDER_RELEVANCE = 0;
    const ORDER_TITLE = 1;
    const ORDER_DOC_LOGIC = 2;

    const ORDER_ASC = 0;
    const ORDER_DESC = 1;

    const VIEW_LIST = 'list';
    const VIEW_TEXT_LIST = 'txtlist';
    const VIEW_THUMBS = 'thumbs';

    private $_show_pics = true;
    private $_results_by_page = 10;
    private $_view = self::VIEW_LIST;
    private $_order = self::ORDER_RELEVANCE;

    private $_request;

    /**
     * Should pictures be displayed
     *
     * @return boolean
     */
    public function showPics()
    {
        return $this->_show_pics;
    }

    /**
     * Set pictures display
     *
     * @param boolean $show Display picturesctures or not
     *
     * @return void
     */
    public function setShowPics($show)
    {
        $this->_show_pics = $show;
    }

    /**
     * Set number of results to display on each page
     *
     * @return int
     */
    public function getResultsbyPage()
    {
        return $this->_results_by_page;
    }

    /**
     * Set number of results to display on each page
     *
     * @param int $count Number of results to display
     *
     * @return void
     */
    public function setResultsByPage($count)
    {
        $this->_results_by_page = $count;
    }

    /**
     * Get current view
     *
     * @return string
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Set view
     *
     * @param string $view New view
     *
     * @return void
     */
    public function setView($view)
    {
        if ( $view === self::VIEW_LIST
            || $view === self::VIEW_TEXT_LIST
            || $view === self::VIEW_THUMBS
        ) {
            if ( $view === self::VIEW_TEXT_LIST ) {
                $this->setShowPics(false);
            } else {
                $this->setShowPics(true);
            }
            $this->_view = $view;
        } else {
            throw new \RuntimeException(
                str_replace(
                    '%s',
                    $view,
                    _('View %s is not known!')
                )
            );
        }
    }

    /**
     * Get order
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Set order
     *
     * @param int $order New order
     *
     * @return void
     */
    public function setOrder($order)
    {
        if ( $order === self::ORDER_RELEVANCE
            || $order === self::ORDER_TITLE
            || $order === self::ORDER_DOC_LOGIC
        ) {
            $this->_order = $order;
        } else {
            throw new \RuntimeException(
                str_replace(
                    '%s',
                    $order,
                    _('Order %s is not known!')
                )
            );
        }
    }

    /**
     * Bind request
     *
     * @param Request $request Request to bind to
     *
     * @return void
     */
    public function bind(Request $request)
    {
        $this->_request = $request;

        if ( $request->get('view') ) {
            $this->setView($request->get('view'));
        }

        if ( $request->get('results_by_page') ) {
            $this->setResultsByPage($request->get('results_by_page'));
        }

        if ( $request->get('results_order')
            || $request->get('results_order') === '0'
        ) {
            $this->setOrder((int)$request->get('results_order'));
        }
    }
}
