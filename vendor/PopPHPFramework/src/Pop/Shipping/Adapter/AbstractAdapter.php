<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Shipping
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Shipping\Adapter;

/**
 * Shipping adapter abstract class
 *
 * @category   Pop
 * @package    Pop_Shipping
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */


abstract class AbstractAdapter implements AdapterInterface
{

    /**
     * Response object
     * @var object
     */
    protected $response = null;

    /**
     * Response code
     * @var int
     */
    protected $responseCode = null;

    /**
     * Response message
     * @var string
     */
    protected $responseMessage = null;

    /**
     * Service rates
     * @var string
     */
    protected $rates = array();

    /**
     * Send transaction
     *
     * @return void
     */
    abstract public function send();

    /**
     * Return whether the transaction is a success
     *
     * @return boolean
     */
    abstract public function isSuccess();

    /**
     * Return whether the transaction is an error
     *
     * @return boolean
     */
    abstract public function isError();

    /**
     * Get response
     *
     * @return object
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get response code
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Get response message
     *
     * @return string
     */
    public function getResponseMessage()
    {
        return $this->responseMessage;
    }

    /**
     * Get service rates
     *
     * @return array
     */
    public function getRates()
    {
        return $this->rates;
    }

}
