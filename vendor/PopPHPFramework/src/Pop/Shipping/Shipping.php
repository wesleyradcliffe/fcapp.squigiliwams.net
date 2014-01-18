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
namespace Pop\Shipping;

/**
 * Shipping class
 *
 * @category   Pop
 * @package    Pop_Shipping
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Shipping
{

    /**
     * Shipping adapter
     * @var mixed
     */
    protected $adapter = null;

    /**
     * Constructor
     *
     * Instantiate the shipping object
     *
     * @param  Adapter\AbstractAdapter $adapter
     * @return \Pop\Shipping\Shipping
     */
    public function __construct(Adapter\AbstractAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Access the adapter
     *
     * @return \Pop\Shipping\Adapter\AbstractAdapter
     */
    public function adapter()
    {
        return $this->adapter;
    }

    /**
     * Set ship to
     *
     * @param  array $shipTo
     * @return self
     */
    public function shipTo(array $shipTo)
    {
        $this->adapter->shipTo($shipTo);
        return $this;
    }

    /**
     * Set ship from
     *
     * @param  array $shipFrom
     * @return self
     */
    public function shipFrom(array $shipFrom)
    {
        $this->adapter->shipFrom($shipFrom);
        return $this;
    }

    /**
     * Set dimensions
     *
     * @param  array  $dimensions
     * @param  string $unit
     * @return self
     */
    public function setDimensions(array $dimensions, $unit = null)
    {
        $this->adapter->setDimensions($dimensions, $unit);
        return $this;
    }

    /**
     * Set dimensions
     *
     * @param  string $weight
     * @param  string $unit
     * @return self
     */
    public function setWeight($weight, $unit = null)
    {
        $this->adapter->setWeight($weight, $unit);
        return $this;
    }

    /**
     * Send transaction data
     *
     * @return void
     */
    public function send()
    {
        $this->adapter->send();
    }

    /**
     * Return whether the transaction is success
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->adapter->isSuccess();
    }

    /**
     * Return whether the transaction is an error
     *
     * @return boolean
     */
    public function isError()
    {
        return $this->adapter->isError();
    }

    /**
     * Get response
     *
     * @return object
     */
    public function getResponse()
    {
        return $this->adapter->getResponse();
    }

    /**
     * Get response code
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->adapter->getResponseCode();
    }

    /**
     * Get response message
     *
     * @return string
     */
    public function getResponseMessage()
    {
        return $this->adapter->getResponseMessage();
    }

    /**
     * Get service rates
     *
     * @return array
     */
    public function getRates()
    {
        return $this->adapter->getRates();
    }

}
