<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Db
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Db;

/**
 * Db class
 *
 * @category   Pop
 * @package    Pop_Db
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Db
{

    /**
     * Default database adapter object
     * @var mixed
     */
    protected $adapter = null;

    /**
     * Constructor
     *
     * Instantiate the database connection object.
     *
     * @param  string $type
     * @param  array  $options
     * @param  string $prefix
     * @throws Exception
     * @return \Pop\Db\Db
     */
    public function __construct($type, array $options, $prefix = 'Pop\Db\Adapter\\')
    {
        $class = $prefix . ucfirst(strtolower($type));

        if (!class_exists($class)) {
            throw new Exception('Error: That database adapter class does not exist.');
        }

        $this->adapter = new $class($options);
    }

    /**
     * Determine whether or not an instance of the DB object exists already,
     * and instantiate the object if it doesn't exist.
     *
     * @param  string $type
     * @param  array  $options
     * @param  string $prefix
     * @return \Pop\Db\Db
     */
    public static function factory($type, array $options, $prefix = 'Pop\Db\Adapter\\')
    {
        return new self($type, $options, $prefix);
    }

    /**
     * Get the database adapter.
     *
     * @return mixed
     */
    public function adapter()
    {
        return $this->adapter;
    }

    /**
     * Get the database adapter type.
     *
     * @return string
     */
    public function getAdapterType()
    {
        $type = null;

        $class = get_class($this->adapter);

        if (stripos($class, 'Pdo') !== false) {
            $type = 'Pdo\\' . ucfirst($this->adapter->getDbtype());
        } else {
            $type = ucfirst(str_replace('Pop\Db\Adapter\\', '', $class));
        }

        return $type;
    }

}
