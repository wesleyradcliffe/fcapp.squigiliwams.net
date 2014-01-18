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
 * Pdf page object class
 *
 * @category   Pop
 * @package    Pop_Pdf
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Page
{

    /**
     * PDF page object index
     * @var int
     */
    public $index = 4;

    /**
     * PDF page object parent index
     * @var int
     */
    public $parent = 2;

    /**
     * PDF page object width
     * @var int
     */
    public $width = 612;

    /**
     * PDF page object height
     * @var int
     */
    public $height = 792;

    /**
     * PDF page object current content object index
     * @var int
     */
    public $curContent = null;

    /**
     * PDF page annotations
     * @var array
     */
    public $annots = array();

    /**
     * PDF page content objects
     * @var array
     */
    public $content = array();

    /**
     * PDF page xobjects
     * @var array
     */
    public $xobjs = array();

    /**
     * PDF page fonts
     * @var array
     */
    public $fonts = array();

    /**
     * PDF page thumb object
     * @var int
     */
    public $thumb = null;

    /**
     * PDF page object data
     * @var string
     */
    protected $data = null;

    /**
     * Array of page sizes
     * @var array
     */
    protected $sizes = array(
        '#10 Envelope' => array('width' => '297', 'height' => '684'),
        'C5 Envelope'  => array('width' => '461', 'height' => '648'),
        'DL Envelope'  => array('width' => '312', 'height' => '624'),
        'Folio'        => array('width' => '595', 'height' => '935'),
        'Executive'    => array('width' => '522', 'height' => '756'),
        'Letter'       => array('width' => '612', 'height' => '792'),
        'Legal'        => array('width' => '612', 'height' => '1008'),
        'Ledger'       => array('width' => '1224', 'height' => '792'),
        'Tabloid'      => array('width' => '792', 'height' => '1224'),
        'A0'           => array('width' => '2384', 'height' => '3370'),
        'A1'           => array('width' => '1684', 'height' => '2384'),
        'A2'           => array('width' => '1191', 'height' => '1684'),
        'A3'           => array('width' => '842', 'height' => '1191'),
        'A4'           => array('width' => '595', 'height' => '842'),
        'A5'           => array('width' => '420', 'height' => '595'),
        'A6'           => array('width' => '297', 'height' => '420'),
        'A7'           => array('width' => '210', 'height' => '297'),
        'A8'           => array('width' => '148', 'height' => '210'),
        'A9'           => array('width' => '105', 'height' => '148'),
        'B0'           => array('width' => '2920', 'height' => '4127'),
        'B1'           => array('width' => '2064', 'height' => '2920'),
        'B2'           => array('width' => '1460', 'height' => '2064'),
        'B3'           => array('width' => '1032', 'height' => '1460'),
        'B4'           => array('width' => '729', 'height' => '1032'),
        'B5'           => array('width' => '516', 'height' => '729'),
        'B6'           => array('width' => '363', 'height' => '516'),
        'B7'           => array('width' => '258', 'height' => '363'),
        'B8'           => array('width' => '181', 'height' => '258'),
        'B9'           => array('width' => '127', 'height' => '181'),
        'B10'          => array('width' => '91', 'height' => '127')
    );

    /**
     * Constructor
     *
     * Instantiate a PDF page object.
     *
     * @param  string $str
     * @param  string $sz
     * @param  string $w
     * @param  string $h
     * @param  string $i
     * @throws Exception
     * @return \Pop\Pdf\Object\Page
     */
    public function __construct($str = null, $sz = null, $w = null, $h = null, $i = null)
    {
        // Use default settings for a new PDF page.
        if (null === $str) {
            // If no arguments are passed, default to the Letter size.
            if ((null === $sz) && (null === $w) && (null === $h)) {
                $this->width = $this->sizes['Letter']['width'];
                $this->height = $this->sizes['Letter']['height'];
            } else {
                // Check for a default size setting.
                if (null !== $sz) {
                    if (array_key_exists($sz, $this->sizes)) {
                        $this->width = $this->sizes[$sz]['width'];
                        $this->height = $this->sizes[$sz]['height'];
                    } else {
                        // Else, assign a custom width and height.
                        if (((null === $w) && (null !== $h)) || ((null !== $w) && (null === $h))) {
                            throw new Exception('Error: A width and height must be passed.');
                        }
                        $this->width = $w;
                        $this->height = $h;
                    }
                } else {
                    // Else, assign a custom width and height.
                    if (((null === $w) && (null !== $h)) || ((null !== $w) && (null === $h))) {
                        throw new Exception('Error: A width and height must be passed.');
                    }
                    $this->width = $w;
                    $this->height = $h;
                }
            }http://www.google.com/

            if (null === $i) {
                throw new Exception('Error: A page index must be passed.');
            }
            $this->index = $i;
            $this->data = "\n[{page_index}] 0 obj\n<</Type/Page/Parent [{parent}] 0 R[{annotations}]/MediaBox[0 0 {$this->width} {$this->height}]/Contents[[{content_objects}]]/Resources<</ProcSet[/PDF/Text/ImageB/ImageC/ImageI][{xobjects}][{fonts}]>>>>\nendobj\n";
        } else {
            // Else, determine the page object index.
            $this->index = substr($str, 0, strpos($str, ' '));

            // If present, record and record any thumb object index, as the contents of the page may change.
            if (strpos($str, '/Thumb') !== false) {
                $t = substr($str, strpos($str, 'Thumb'));
                $t = substr($t, 0, strpos($t, '/'));
                $t = str_replace('Thumb', '', $t);
                $t = str_replace('0 R', '', $t);
                $t = str_replace(' ', '', $t);
                $this->thumb = $t;
            }

            // Determine the page parent object index.
            $par = substr($str, strpos($str, 'Parent'));
            $par = substr($par, 0, strpos($par, '/'));
            $par = str_replace('Parent', '', $par);
            $par = str_replace('0 R', '', $par);
            $par = str_replace(' ', '', $par);
            $this->parent = $par;

            // Determine the page width and height.
            $wh = substr($str, strpos($str, 'MediaBox'));
            $wh = substr($wh, 0, (strpos($wh, ']') + 1));
            $wh = (strpos($wh, 'MediaBox [') !== false) ? str_replace('MediaBox [', '', $wh) : str_replace('MediaBox[', '', $wh);
            $wh = str_replace(']', '', $wh);
            $whAry = explode(' ', $wh);
            $this->width = $whAry[2];
            $this->height = $whAry[3];

            // Determine the page content objects.
            $cn = substr($str, strpos($str, 'Contents'));
            if (strpos($cn , '/') !== false) {
                $cn = substr($cn, 0, strpos($cn, '/'));
            } else if (strpos($cn , '>') !== false) {
                $cn = substr($cn, 0, strpos($cn, '>'));
            }
            $cn = str_replace('Contents', '', $cn);
            if (strpos($cn, '[') !== false) {
                $cn = str_replace('[', '', $cn);
                $cn = str_replace(']', '', $cn);
                $cn = str_replace('0 R', '|', $cn);
                $cn = str_replace(' ', '', $cn);
                $cn = explode('|', $cn);
                foreach ($cn as $value) {
                    if ($value != '') {
                        $this->content[] = $value;
                    }
                }
            } else {
                $cn = str_replace('0 R', '', $cn);
                $cn = str_replace(' ', '', $cn);
                $this->content[] = $cn;
            }

            // If they exist, determine the page annotation objects.
            if (strpos($str, '/Annots') !== false) {
                $an = substr($str, strpos($str, 'Annots'));
                $an = substr($an, 0, strpos($an, '/'));
                $an = str_replace('Annots', '', $an);
                if (strpos($an, '[') !== false) {
                    $an = str_replace('[', '', $an);
                    $an = str_replace(']', '', $an);
                    $an = str_replace('0 R', '|', $an);
                    $an = str_replace(' ', '', $an);
                    $an = explode('|', $an);
                    foreach ($an as $value) {
                        if ($value != '') {
                            $this->annots[] = $value;
                        }
                    }
                } else {
                    $an = str_replace('0 R', '', $an);
                    $an = str_replace(' ', '', $an);
                    $this->annots[] = $an;
                }
            }

            // If they exist, determine the page fonts.
            if (strpos($str, '/Font') !== false) {
                $ft = substr($str, strpos($str, 'Font'));
                $ft = substr($ft, 0, (strpos($ft, '>>') + 2));
                $ft = str_replace('Font<<', '', $ft);
                $ft = str_replace('>>', '', $ft);
                $ft = explode('/', $ft);
                foreach ($ft as $value) {
                    if ($value != '') {
                        $this->fonts[] = '/' . $value;
                    }
                }
            }

            // If they exist, determine the page xobjects.
            if (strpos($str, '/XObject') !== false) {
                $xo = substr($str, strpos($str, 'XObject'));
                $xo = substr($xo, 0, (strpos($xo, '>>') + 2));
                $xo = str_replace('XObject<<', '', $xo);
                $xo = str_replace('>>', '', $xo);
                $xo = explode('/', $xo);
                foreach ($xo as $value) {
                    if ($value != '') {
                        $this->xobjs[] = '/' . $value;
                    }
                }
            }

            // If they exist, determine the page graphic states.
            if (strpos($str, '/ExtGState') !== false) {
                $gs = substr($str, strpos($str, 'ExtGState'));
                $gs = substr($gs, 0, (strpos($gs, '>>') + 2));
                $gs = '/' . $gs;
            } else {
                $gs = '';
            }

            // If any groups exist
            if (strpos($str, '/Group') !== false) {
                $grp = substr($str, strpos($str, 'Group'));
                $grp = substr($grp, 0, (strpos($grp, '>>') + 2));
                $grp = '/' . $grp;
            } else {
                $grp = '';
            }

            // If resources exists
            if (strpos($str, '/Resources') !== false) {
                $res = substr($str, strpos($str, 'Resources'));
                if (strpos($res, '0 R') !== false) {
                    $res = substr($res, 0, (strpos($res, '0 R') + 3));
                    $res = '/' . $res;

                } else if (strpos($res, '>>') !== false) {
                    $res = substr($res, 0, (strpos($res, '>>') + 2));
                    $res = '/' . $res;
                } else {
                    $res = "/Resources<</ProcSet[/PDF/Text/ImageB/ImageC/ImageI][{xobjects}][{fonts}]{$gs}>>";
                }
            } else {
                $res = "/Resources<</ProcSet[/PDF/Text/ImageB/ImageC/ImageI][{xobjects}][{fonts}]{$gs}>>";
            }

            $this->data = "\n[{page_index}] 0 obj\n<</Type/Page/Parent [{parent}] 0 R[{annotations}]/MediaBox[0 0 {$this->width} {$this->height}]{$grp}/Contents[[{content_objects}]]{$res}>>\nendobj\n";
        }
    }

    /**
     * Method to print the parent object.
     *
     * @return string
     */
    public function __toString()
    {
        // Format the content objects.
        $content_objs = implode(" 0 R ", $this->content);
        $content_objs .= " 0 R";

        // Format the annotations.
        if (count($this->annots) > 0) {
            $annots = '/Annots[';
            $annots .= implode(" 0 R ", $this->annots);
            $annots .= " 0 R]";
        } else {
            $annots = '';
        }

        // Format the xobjects.
        if (count($this->xobjs) > 0) {
            $xobjects = '/XObject<<';
            $xobjects .= implode('', $this->xobjs);
            $xobjects .= '>>';
        } else {
            $xobjects = '';
        }

        // Format the fonts.
        if (count($this->fonts) > 0) {
            $fonts = '/Font<<';
            $fonts .= implode('', $this->fonts);
            $fonts .= '>>';
        } else {
            $fonts = '';
        }

        // Swap out the placeholders.
        $obj = str_replace('[{page_index}]', $this->index, $this->data);
        $obj = str_replace('[{parent}]', $this->parent, $obj);
        $obj = str_replace('[{annotations}]', $annots, $obj);
        $obj = str_replace('[{xobjects}]', $xobjects, $obj);
        $obj = str_replace('[{fonts}]', $fonts, $obj);
        $obj = str_replace('[{content_objects}]', $content_objs, $obj);

        return $obj;
    }

}
