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
 * Mcrypt class
 *
 * @category   Pop
 * @package    Pop_Crypt
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Mcrypt implements CryptInterface
{

    /**
     * Cipher
     * @var int
     */
    protected $cipher = null;

    /**
     * Mode
     * @var int
     */
    protected $mode = null;

    /**
     * Source
     * @var int
     */
    protected $source = null;

    /**
     * Salt
     * @var string
     */
    protected $salt = null;

    /**
     * IV
     * @var string
     */
    protected $iv = null;

    /**
     * IV size
     * @var int
     */
    protected $ivSize = 0;

    /**
     * Constructor
     *
     * Instantiate the mcrypt object.
     *
     * @param  int $cipher
     * @param  int $mode
     * @param  int $source
     * @throws Exception
     * @return self
     */
    public function __construct($cipher = null, $mode = null, $source = null)
    {
        if (!function_exists('mcrypt_encrypt')) {
            throw new Exception('Error: The mcrypt extension is not installed.');
        }
        $this->setCipher($cipher);
        $this->setMode($mode);
        $this->setSource($source);
    }

    /**
     * Method to set the cipher
     *
     * @param  int $cipher
     * @return self
     */
    public function setCipher($cipher = null)
    {
        $this->cipher = (null !== $cipher) ? $cipher : MCRYPT_RIJNDAEL_256;
        return $this;
    }

    /**
     * Method to get the cipher
     *
     * @return int
     */
    public function getCipher()
    {
        return $this->cipher;
    }

    /**
     * Method to set the mode
     *
     * @param  int $mode
     * @return self
     */
    public function setMode($mode = null)
    {
        $this->mode = (null !== $mode) ? $mode : MCRYPT_MODE_CBC;
        return $this;
    }

    /**
     * Method to get the mode
     *
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Method to set the source
     *
     * @param  int $source
     * @return self
     */
    public function setSource($source = null)
    {
        $this->source = (null !== $source) ? $source : MCRYPT_RAND;
        return $this;
    }

    /**
     * Method to get the source
     *
     * @return int
     */
    public function getSource()
    {
        return $this->source;
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
     * Method to get the iv
     *
     * @return string
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * Method to get the iv size
     *
     * @return int
     */
    public function getIvSize()
    {
        return $this->ivSize;
    }

    /**
     * Method to create the hashed value
     *
     * @param  string $string
     * @return string
     */
    public function create($string)
    {
        $hash = null;

        $this->ivSize = mcrypt_get_iv_size($this->cipher, $this->mode);

        $this->salt = (null === $this->salt) ?
            substr(str_replace('+', '.', base64_encode(String::random(32))), 0, $this->ivSize) :
            substr(str_replace('+', '.', base64_encode($this->salt)), 0, $this->ivSize);

        $this->iv = mcrypt_create_iv($this->ivSize, $this->source);

        $hash = mcrypt_encrypt($this->cipher, $this->salt, $string, $this->mode, $this->iv);
        $hash = base64_encode($this->iv . $this->salt . '$' . $hash);

        return $hash;
    }

    /**
     * Method to decrypt the hashed value
     *
     * @param  string $hash
     * @return string
     */
    public function decrypt($hash)
    {
        if ($this->ivSize == 0) {
            $this->ivSize = mcrypt_get_iv_size($this->cipher, $this->mode);
        }

        $decrypted = base64_decode($hash);

        $this->iv = substr($decrypted, 0, $this->ivSize);
        if (null === $this->salt) {
            $this->salt = substr($decrypted, $this->ivSize);
            $this->salt = substr($this->salt, 0, strpos($this->salt, '$'));
        }
        $decrypted = substr($decrypted, ($this->ivSize + strlen($this->salt) + 1));
        return trim(mcrypt_decrypt($this->cipher, $this->salt, $decrypted, $this->mode, $this->iv));
    }

    /**
     * Method to verify the hashed value
     *
     * @param  string $string
     * @param  string $hash
     * @return boolean
     */
    public function verify($string, $hash)
    {
        if ($this->ivSize == 0) {
            $this->ivSize = mcrypt_get_iv_size($this->cipher, $this->mode);
        }

        $decrypted = base64_decode($hash);

        $this->iv = substr($decrypted, 0, $this->ivSize);
        if (null === $this->salt) {
            $this->salt = substr($decrypted, $this->ivSize);
            $this->salt = substr($this->salt, 0, strpos($this->salt, '$'));
        }
        $decrypted = substr($decrypted, ($this->ivSize + strlen($this->salt) + 1));
        $decrypted = trim(mcrypt_decrypt($this->cipher, $this->salt, $decrypted, $this->mode, $this->iv));

        return ($string === $decrypted);
    }

}
