<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Auth
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Auth;

/**
 * Auth resource class
 *
 * @category   Pop
 * @package    Pop_Auth
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Resource
{

    /**
     * Resource name
     * @var string
     */
    protected $name = null;

    /**
     * Constructor
     *
     * Instantiate the resource object
     *
     * @param  string $name
     * @return \Pop\Auth\Resource
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Static method to instantiate the resource object and return itself
     * to facilitate chaining methods together.
     *
     * @param  string $name
     * @return \Pop\Auth\Resource
     */
    public static function factory($name)
    {
        return new self($name);
    }

    /**
     * Method to get the role name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Method to return the string value of the name of the role
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

}
