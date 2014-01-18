<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Archive
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Archive\Adapter;

use Pop\File\Dir;

/**
 * Zip archive adapter class
 *
 * @category   Pop
 * @package    Pop_Archive
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Zip implements ArchiveInterface
{

    /**
     * ZipArchive object
     * @var \ZipArchive
     */
    protected $archive = null;

    /**
     * Archive path
     * @var string
     */
    protected $path = null;

    /**
     * Working directory
     * @var string
     */
    protected $workingDir = null;

    /**
     * Method to instantiate an archive adapter object
     *
     * @param  \Pop\Archive\Archive $archive
     * @return \Pop\Archive\Adapter\Zip
     */
    public function __construct(\Pop\Archive\Archive $archive)
    {
        if (strpos($archive->getFullpath(), '/.') !== false) {
            $this->workingDir = substr($archive->getFullpath(), 0, strpos($archive->getFullpath(), '/.'));
        } else if (strpos($archive->getFullpath(), '\\.') !== false) {
            $this->workingDir = substr($archive->getFullpath(), 0, strpos($archive->getFullpath(), '\\.'));
        } else {
            $this->workingDir = getcwd();
        }

        if ((substr($archive->getFullpath(), 0, 1) != '/') && (substr($archive->getFullpath(), 1, 1) != ':')) {
            $this->path = $this->workingDir . DIRECTORY_SEPARATOR . $archive->getFullpath();
        } else {
            $this->path = realpath(dirname($archive->getFullpath())) . DIRECTORY_SEPARATOR . $archive->getBasename();
        }
        $this->archive = new \ZipArchive();
    }

    /**
     * Method to return the archive object
     *
     * @return mixed
     */
    public function archive()
    {
        return $this->archive;
    }

    /**
     * Method to extract an archived and/or compressed file
     *
     * @param  string $to
     * @return void
     */
    public function extract($to = null)
    {
        if ($this->archive->open($this->path) === true) {
            $path = (null !== $to) ? realpath($to) : './';
            $this->archive->extractTo($path);
            $this->archive->close();
        }
    }

    /**
     * Method to create an archive file
     *
     * @param  string|array $files
     * @return void
     */
    public function addFiles($files)
    {
        if (is_array($files)) {
            foreach ($files as $key => $value) {
                $files[$key] = realpath($value);
            }
        } else {
            $files = array(realpath($files));
        }

        $result = (!file_exists($this->path)) ?
            $this->archive->open($this->path, \ZipArchive::CREATE) :
            $this->archive->open($this->path);

        if ($result === true) {
            foreach ($files as $file) {
                if (!is_dir($file)) {
                    $this->archive->addFile($file, basename($file));
                } else {
                    $this->addDir($file);
                }
            }
            $this->archive->close();
        }
    }

    /**
     * Method to create sub directories within the zip archive
     *
     * @param  array $branch
     * @param  string $level
     * @param  string $orig
     * @return void
     */
    public function addDir($branch, $level = null, $orig = null)
    {
        if (!is_array($branch)) {
            $dir = new Dir($branch);
            $branch = $dir->getTree();
        }

        foreach ($branch as $leaf => $node) {
            if (is_array($node)) {
                if (null === $level) {
                    $new = basename($leaf);
                    $orig = substr($leaf, 0, strrpos($leaf, $new));
                } else {
                    $new = $level . $leaf;
                }
                $this->archive->addEmptyDir($new);
                $this->addDir($node, $new, $orig);
            } else {
                $this->archive->addFile($orig . $level . '/' . $node, $level . '/' . $node);
            }
        }
    }

    /**
     * Method to return a listing of the contents of an archived file
     *
     * @param  boolean $full
     * @return array
     */
    public function listFiles($full = false)
    {
        $files = array();
        $list = array();

        if ($this->archive->open($this->path) === true) {
            $i = 0;
            while ($this->archive->statIndex($i)) {
                $list[] = $this->archive->statIndex($i);
                $i++;
            }
        }

        if (!$full) {
            foreach ($list as $file) {
                $files[] = $file['name'];
            }
        } else {
            $files = $list;
        }

        return $files;
    }

    /**
     * Method to return an array of all the directories in the archive
     *
     * @return array
     */
    public function getDirs()
    {
        $dirs = array();

        $list = $this->listFiles(true);

        foreach ($list as $entry) {
            if ($entry['size'] == 0) {
                $dirs[] = $entry['name'];
            }
        }

        return $dirs;
    }

}
