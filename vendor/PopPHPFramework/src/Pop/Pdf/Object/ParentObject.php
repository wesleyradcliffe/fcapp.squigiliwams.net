<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Pdf
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Pdf\Object;

/**
 * Pdf parent object class
 *
 * @category   Pop
 * @package    Pop_Pdf
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class ParentObject
{

    /**
     * PDF parent object index
     * @var int
     */
    public $index = 2;

    /**
     * PDF parent kids count
     * @var int
     */
    public $count = 0;

    /**
     * PDF parent kids object indices
     * @var array
     */
    public $kids = array();

    /**
     * PDF parent object data
     * @var string
     */
    protected $data = null;

    /**
     * Constructor
     *
     * Instantiate a PDF parent object.
     *
     * @param  string $str
     * @return \Pop\Pdf\Object\ParentObject
     */
    public function __construct($str = null)
    {
        $matches = array();

        // Use default settings for a new PDF and its parent object.
        if (null === $str) {
            $this->data = "2 0 obj\n<</Type/Pages/Count [{count}]/Kids[[{kids}]]>>\nendobj\n";
        } else {
            // Else, determine the parent object index.
            $this->index = substr($str, 0, strpos($str, ' '));

            // Determine the kids count.
            preg_match('/\/Count\s\d*/', $str, $matches);
            $c = $matches[0];
            $c = str_replace('/Count ', '', $c);
            $str = str_replace('Count ' . $c, 'Count [{count}]', $str);

            // Determine the kids object indices.
            $k = substr($str, (strpos($str, '/Kids') + 5), strpos($str, ']'));
            $k = substr($k, 0, (strpos($k, ']') + 1));
            $str = str_replace($k, '[[{kids}]]', $str);
            $k = str_replace(' ', '', $k);
            $k = str_replace('[', '', $k);
            $k = str_replace(']', '', $k);
            $k = str_replace('0R', '|', $k);
            $k = substr($k, 0, -1);

            // Kids clean up.
            $kAry = explode('|', $k);
            foreach ($kAry as $key => $value) {
                if ($value == ''){
                    unset($kAry[$key]);
                }
            }

            // Set the kids array, the count and the parent data.
            $this->kids = $kAry;
            $this->count = $c;
            $this->data = $str . "\n";
        }
    }

    /**
     * Method to print the parent object.
     *
     * @return string
     */
    public function __toString()
    {
        // Format the kids array.
        $kids = implode(" 0 R ", $this->kids);
        $kids .= " 0 R";

        // Swap out the placeholders.
        $obj = str_replace('[{count}]', $this->count, $this->data);
        $obj = str_replace('[{kids}]', $kids, $obj);

        return $obj;
    }

}
