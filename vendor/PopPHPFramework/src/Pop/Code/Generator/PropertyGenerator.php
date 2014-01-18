<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Code
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Code\Generator;

/**
 * Property generator code class
 *
 * @category   Pop
 * @package    Pop_Code
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class PropertyGenerator
{

    /**
     * Docblock generator object
     * @var \Pop\Code\Generator\DocblockGenerator
     */
    protected $docblock = null;

    /**
     * Property type
     * @var string
     */
    protected $type = null;

    /**
     * Property name
     * @var string
     */
    protected $name = null;

    /**
     * Property visibility
     * @var string
     */
    protected $visibility = 'public';

    /**
     * Property static flag
     * @var boolean
     */
    protected $static = false;

    /**
     * Property value
     * @var mixed
     */
    protected $value = null;

    /**
     * Property indent
     * @var string
     */
    protected $indent = '    ';

    /**
     * Property output
     * @var string
     */
    protected $output = null;

    /**
     * Constructor
     *
     * Instantiate the property generator object
     *
     * @param  string $name
     * @param  string $type
     * @param  mixed  $value
     * @param  string $visibility
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public function __construct($name, $type, $value = null, $visibility = 'public')
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->visibility = $visibility;
    }

    /**
     * Static method to instantiate the property generator object and return itself
     * to facilitate chaining methods together.
     *
     * @param  string $name
     * @param  string $type
     * @param  mixed  $value
     * @param  string $visibility
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public static function factory($name, $type, $value = null, $visibility = 'public')
    {
        return new self($name, $type, $value, $visibility);
    }

    /**
     * Set the property static flag
     *
     * @param  boolean $static
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public function setStatic($static = false)
    {
        $this->static = (boolean)$static;
        return $this;
    }

    /**
     * Get the property static flag
     *
     * @return boolean
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * Set the property description
     *
     * @param  string $desc
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public function setDesc($desc = null)
    {
        if (null !== $this->docblock) {
            $this->docblock->setDesc($desc);
        } else {
            $this->docblock = new DocblockGenerator($desc, $this->indent);
        }
        return $this;
    }

    /**
     * Get the property description
     *
     * @return string
     */
    public function getDesc()
    {
        $desc = null;
        if (null !== $this->docblock) {
            $desc = $this->docblock->getDesc();
        }
        return $desc;
    }

    /**
     * Set the property indent
     *
     * @param  string $indent
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public function setIndent($indent = null)
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * Get the property indent
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Set the property type
     *
     * @param  string $type
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the property type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the property name
     *
     * @param  string $name
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the property name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the property value
     *
     * @param  mixed $value
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public function setValue($value = null)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the property value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the property visibility
     *
     * @param  string $visibility
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public function setVisibility($visibility = 'public')
    {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * Get the property visibility
     *
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set the docblock generator object
     *
     * @param  DocblockGenerator $docblock
     * @return \Pop\Code\Generator\PropertyGenerator
     */
    public function setDocblock(DocblockGenerator $docblock)
    {
        $this->docblock = $docblock;
        return $this;
    }

    /**
     * Access the docblock generator object
     *
     * @return \Pop\Code\Generator\DocblockGenerator
     */
    public function getDocblock()
    {
        return $this->docblock;
    }

    /**
     * Render property
     *
     * @param  boolean $ret
     * @return mixed
     */
    public function render($ret = false)
    {
        $static = null;
        if ($this->visibility != 'const') {
            $varDeclaration = ' $';
            if ($this->static) {
                $static = ' static';
            }
        } else {
            $varDeclaration = ' ';
        }

        if (null === $this->docblock) {
            $this->docblock = new DocblockGenerator(null, $this->indent);
        }
        $this->docblock->setTag('var', $this->type);
        $this->output = PHP_EOL . $this->docblock->render(true);
        $this->output .= $this->indent . $this->visibility . $static . $varDeclaration . $this->name;

        if (null !== $this->value) {
            if ($this->type == 'array') {
                $val = (count($this->value) == 0) ? 'array()' : $this->formatArrayValues();
                $this->output .= ' = ' . $val . PHP_EOL;
            } else if (($this->type == 'integer') || ($this->type == 'int') || ($this->type == 'float')) {
                $this->output .= ' = ' . $this->value . ';';
            } else if ($this->type == 'boolean') {
                $val = ($this->value) ? 'true' : 'false';
                $this->output .= " = " . $val . ";";
            } else {
                $this->output .= " = '" . $this->value . "';";
            }
        } else {
            $val = ($this->type == 'array') ? 'array()' : 'null';
            $this->output .= ' = ' . $val . ';';
        }

        if ($ret) {
            return $this->output;
        } else {
            echo $this->output;
        }
    }

    /**
     * Format array value
     *
     * @return string
     */
    protected function formatArrayValues()
    {
        $ary = str_replace(PHP_EOL, PHP_EOL . $this->indent . '  ', var_export($this->value, true));
        $ary .= ';';
        $ary = str_replace('  );', ');', $ary);
        $ary = str_replace('NULL', 'null', $ary);

        $keys = array_keys($this->value);

        $isAssoc = false;

        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] != $i) {
                $isAssoc = true;
            }
        }

        if (!$isAssoc) {
            for ($i = 0; $i < count($keys); $i++) {
                $ary = str_replace($i . ' => ', '', $ary);
            }
        }

        return $ary;
    }

    /**
     * Print property
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render(true);
    }

}
