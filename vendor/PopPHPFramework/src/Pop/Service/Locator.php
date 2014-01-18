<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Service
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Service;

/**
 * Service locator class
 *
 * @category   Pop
 * @package    Pop_Service
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Locator
{

    /**
     * Recursion depth level tracker
     * @var array
     */
    private static $depth = 0;

    /**
     * Recursion called service name tracker
     * @var array
     */
    private static $called = array();

    /**
     * Services
     * @var array
     */
    protected $services = array();

    /**
     * Services that are loaded (instantiated)
     * @var array
     */
    protected $loaded = array();

    /**
     * Constructor
     *
     * Instantiate the service locator object. The optional $services
     * parameter can contain a closure or an array of call/param keys
     * that define what to call when the service is needed.
     * Valid examples are ('params' are optional):
     *
     *     $services = array(
     *         'service1' => function($locator) {...},
     *         'service2' => array(
     *             'call'   => 'SomeClass',
     *             'params' => array(...)
     *         ),
     *         'service3' => array(
     *             'call'   => 'SomeClass',
     *             'params' => function() {...}
     *         )
     *     );
     *
     * @param  array $services
     * @throws Exception
     * @return \Pop\Service\Locator
     */
    public function __construct(array $services = null)
    {
        if (null !== $services) {
            foreach ($services as $name => $service) {
                if ($service instanceof \Closure) {
                    $call = $service;
                    $params = null;
                } else if (is_object($service)) {
                    $call = $service;
                    $params = null;
                } else if (isset($service['call'])) {
                    $call = $service['call'];
                    $params = (isset($service['params'])) ? $service['params'] : null;
                } else {
                    throw new Exception('Error: The $services configuration parameter was not valid.');
                }
                $this->set($name, $call, $params);
            }
        }
    }

    /**
     * Set a service object. It will overwrite
     * any previous service with the same name.
     *
     * @param  string $name
     * @param  mixed  $call
     * @param  mixed  $params
     * @return \Pop\Service\Locator
     */
    public function set($name, $call, $params = null)
    {
        $this->services[$name] = array(
            'call'   => $call,
            'params' => $params
        );
        return $this;
    }

    /**
     * Get a service object.
     *
     * @param  string $name
     * @return mixed
     */
    public function get($name)
    {
        if (!isset($this->services[$name])) {
            return null;
        } else {
            if (!isset($this->loaded[$name])) {
                $this->load($name);
            }
            return $this->loaded[$name];
        }
    }

    /**
     * Remove a service
     *
     * @param  string $name
     * @return \Pop\Service\Locator
     */
    public function remove($name)
    {
        if (isset($this->services[$name])) {
            unset($this->services[$name]);
        }
        if (isset($this->loaded[$name])) {
            unset($this->loaded[$name]);
        }
        return $this;
    }

    /**
     * Load a service object. It will overwrite
     * any previous service with the same name.
     *
     * @param  string $name
     * @throws Exception
     * @return \Pop\Service\Locator
     */
    protected function load($name)
    {
        if (self::$depth > 99) {
            throw new Exception('Error: Possible recursion loop detected when attempting to load these services: ' . implode(', ', self::$called));
        }

        self::$depth++;
        $call = $this->services[$name]['call'];
        $params = $this->services[$name]['params'];

        // If the callable is a closure
        if ($call instanceof \Closure) {
            if (!in_array($name, self::$called)) {
                self::$called[] = $name;
            }
            $obj = call_user_func_array($call, array($this));
        // If the callable is a string
        } else if (is_string($call)) {
            // If there are params
            if (null !== $params) {
                // If the params are a closure
                if ($params instanceof \Closure) {
                    $params = call_user_func_array($params, array($this));
                }
                // If the callable is a static call
                if (strpos($call, '::')) {
                    $obj = call_user_func_array($call, $params);
                // If the callable is a instance call
                } else if (strpos($call, '->')) {
                    $ary = explode('->', $call);
                    $class = $ary[0];
                    $method = $ary[1];
                    $obj = call_user_func_array(array(new $class(), $method), $params);
                // Else, if the callable is a new instance/construct call
                } else {
                    $reflect  = new \ReflectionClass($call);
                    $obj = $reflect->newInstanceArgs($params);
                }
            // Else, just call it
            } else {
                // If the callable is a static call
                if (strpos($call, '::')) {
                    $obj = call_user_func($call);
                // If the callable is a instance call
                } else if (strpos($call, '->')) {
                    $ary = explode('->', $call);
                    $class = $ary[0];
                    $method = $ary[1];
                    $obj = call_user_func(array(new $class(), $method));
                // Else, if the callable is a new instance/construct call
                } else {
                    $obj = new $call();
                }
            }
        // If the callable is already an instantiated object
        } else if (is_object($call)) {
            $obj = $call;
        // Else, throw exception
        } else {
            throw new Exception('Error: The $call parameter must be an object or something callable.');
        }

        $this->loaded[$name] = $obj;
        self::$depth--;
        return $this;
    }

}
