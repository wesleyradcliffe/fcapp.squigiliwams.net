<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Crypt
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Crypt;

use Pop\Filter\String;

/**
 * Bcrypt class
 *
 * @category   Pop
 * @package    Pop_Crypt
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Bcrypt implements CryptInterface
{

    /**
     * Cost
     * @var string
     */
    protected $cost = '08';

    /**
     * Prefix
     * @var string
     */
    protected $prefix = '$2y$';

    /**
     * Salt
     * @var string
     */
    protected $salt = null;

    /**
     * Constructor
     *
     * Instantiate the bcrypt object.
     *
     * @param  string $cost
     * @param  string $prefix
     * @throws Exception
     * @return self
     */
    public function __construct($cost = '08', $prefix = '$2y$')
    {
        if (CRYPT_BLOWFISH == 0) {
            throw new Exception('Error: Blowfish hashing is not supported on this system.');
        }
        $this->setCost($cost);
        $this->setPrefix($prefix);
    }

    /**
     * Method to set the cost
     *
     * @param  string $cost
     * @return self
     */
    public function setCost($cost = '08')
    {
        if ((int)$cost < 4) {
            $cost = '04';
        }
        if ((int)$cost > 31) {
            $cost = '31';
        }

        $this->cost = $cost;
        return $this;
    }

    /**
     * Method to get the cost
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Method to set the cost
     *
     * @param  string $prefix
     * @return self
     */
    public function setPrefix($prefix = '$2y$')
    {
        if (($prefix != '$2a$') && ($prefix != '$2x$') && ($prefix != '$2y$')) {
            $prefix = '$2y$';
        }

        if (version_compare(PHP_VERSION, '5.3.7') < 0) {
            $prefix = '$2a$';
        }
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Method to get the prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Method to set the salt
     *
     * @param  string $salt
     * @return self
     */
    public function setSalt($salt = null)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * Method to get the salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Method to create the hashed value
     *
     * @param  string $string
     * @throws Exception
     * @return string
     */
    public function create($string)
    {
        $hash = null;

        $this->salt = (null === $this->salt) ?
            substr(str_replace('+', '.', base64_encode(String::random(32))), 0, 22) :
            substr(str_replace('+', '.', base64_encode($this->salt)), 0, 22);

        $hash = crypt($string, $this->prefix . $this->cost . '$' . $this->salt);

        if (strlen($hash) < 13) {
            throw new Exception('Error: There was an error with the bcrypt generation.');
        }

        return $hash;
    }

    /**
     * Method to verify the hashed value
     *
     * @param  string $string
     * @param  string $hash
     * @throws Exception
     * @return boolean
     */
    public function verify($string, $hash)
    {
        $result = crypt($string, $hash);

        if (strlen($result) < 13) {
            throw new Exception('Error: There was an error with the bcrypt verification.');
        }

        return ($result === $hash);
    }

}
