<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Color
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Color;

/**
 * Color class
 *
 * @category   Pop
 * @package    Pop_Color
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Color
{

    /**
     * Constant to set the get*() return type to associative array
     * @var int
     */
    const ASSOC_ARRAY = 1;

    /**
     * Constant to set the get*() return type to numeric array
     * @var int
     */
    const NUM_ARRAY = 2;

    /**
     * Constant to set the get*() return type to comma-separated string
     * @var int
     */
    const STRING = 3;

    /**
     * Color space objects
     * @var array
     */
    protected $colors = array();

    /**
     * Constructor
     *
     * Instantiate the color object
     *
     * @param  Space\ColorInterface $color
     * @return \Pop\Color\Color
     */
    public function __construct(Space\ColorInterface $color)
    {
        $this->addColor($color);
    }

    /**
     * Static method to instantiate the color object and return itself
     * to facilitate chaining methods together.
     *
     * @param  Space\ColorInterface $color
     * @return \Pop\Color\Color
     */
    public static function factory(Space\ColorInterface $color)
    {
        return new self($color);
    }

    /**
     * Method to add a new color space object to the color object.
     *
     * @param  Space\ColorInterface $color
     * @return \Pop\Color\Color
     */
    public function addColor(Space\ColorInterface $color)
    {
        $class = get_class($color);

        $type = strtolower(substr($class, (strrpos($class, '\\') + 1)));
        $this->colors[$type] = $color;

        if ($type != 'cmyk') {
            $this->colors['cmyk'] = Convert::toCmyk($color);
        }
        if ($type != 'hex') {
            $this->colors['hex'] = Convert::toHex($color);
        }
        if ($type != 'hsb') {
            $this->colors['hsb'] = Convert::toHsb($color);
        }
        if ($type != 'lab') {
            $this->colors['lab'] = Convert::toLab($color);
        }
        if ($type != 'rgb') {
            $this->colors['rgb'] = Convert::toRgb($color);
        }

        ksort($this->colors);

        return $this;
    }

    /**
     * Get method to return the value of colors[$name].
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->colors) ? $this->colors[$name] : null;
    }

    /**
     * Return the isset value of colors[$name].
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->colors[strtolower($name)]);
    }

    /**
     * Unset colors[$name].
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $name = strtolower($name);

        if (isset($this->colors[$name])) {
            unset($this->colors[$name]);
        }
    }

}
