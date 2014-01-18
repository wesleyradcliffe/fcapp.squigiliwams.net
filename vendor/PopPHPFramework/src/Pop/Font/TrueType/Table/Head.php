<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Font
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Font\TrueType\Table;

/**
 * HEAD table class
 *
 * @category   Pop
 * @package    Pop_Font
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Head
{

    /**
     * Header info
     * @var array
     */
    protected $headerInfo = array();

    /**
     * Constructor
     *
     * Instantiate a TTF 'head' table object.
     *
     * @param  \Pop\Font\AbstractFont $font
     * @return \Pop\Font\TrueType\Table\Head
     */
    public function __construct(\Pop\Font\AbstractFont $font)
    {
        $bytePos = $font->tableInfo['head']->offset;

        $tableVersionNumberBytes = $font->read($bytePos, 4);
        $tableVersionNumber = $font->readFixed(16, 16, $tableVersionNumberBytes);

        $bytePos += 4;

        $fontRevisionBytes = $font->read($bytePos, 4);
        $fontRevision = $font->readFixed(16, 16, $fontRevisionBytes);

        $versionArray = array(
            'tableVersionNumber' => $tableVersionNumber,
            'fontRevision'       => $fontRevision
        );

        $bytePos += 4;

        $headerArray = unpack(
            'NcheckSumAdjustment/' .
            'NmagicNumber/' .
            'nflags/' .
            'nunitsPerEm', $font->read($bytePos, 12)
        );

        $bytePos += 28;
        $bBox = unpack(
            'nxMin/' .
            'nyMin/' .
            'nxMax/' .
            'nyMax', $font->read($bytePos, 8)
        );
        $bBox = $font->shiftToSigned($bBox);

        $bytePos += 14;
        $indexToLocFormat = unpack('nindexToLocFormat', $font->read($bytePos, 2));
        $headerArray['indexToLocFormat'] = $font->shiftToSigned($indexToLocFormat['indexToLocFormat']);

        $this->headerInfo = array_merge($versionArray, $headerArray, $bBox);
    }

    /**
     * Set method to set the property to the value of headerInfo[$name].
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->headerInfo[$name] = $value;
    }

    /**
     * Get method to return the value of headerInfo[$name].
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return (array_key_exists($name, $this->headerInfo)) ? $this->headerInfo[$name] : null;
    }

    /**
     * Return the isset value of headerInfo[$name].
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->headerInfo[$name]);
    }

    /**
     * Unset headerInfo[$name].
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $this->headerInfo[$name] = null;
    }

}
