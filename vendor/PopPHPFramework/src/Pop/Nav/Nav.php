<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Nav
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Nav;

use Pop\Dom\Child;

/**
 * Nav class
 *
 * @category   Pop
 * @package    Pop_Nav
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Nav
{

    /**
     * Nav tree
     * @var array
     */
    protected $tree = array();

    /**
     * Nav config
     * @var array
     */
    protected $config = array();

    /**
     * Acl object
     * @var \Pop\Auth\Acl
     */
    protected $acl = null;

    /**
     * Role object
     * @var \Pop\Auth\Role
     */
    protected $role = null;

    /**
     * Nav parent level
     * @var int
     */
    protected $parentLevel = 1;

    /**
     * Nav child level
     * @var int
     */
    protected $childLevel = 1;

    /**
     * Parent nav element
     * @var \Pop\Dom\Child
     */
    protected $nav = null;

    /**
     * Constructor
     *
     * Instantiate the nav object
     *
     * @param  array $tree
     * @param  array $config
     * @return self
     */
    public function __construct(array $tree = null, array $config = null)
    {
        $this->setTree($tree);
        $this->setConfig($config);
    }

    /**
     * Static method to instantiate the nav object and return itself
     * to facilitate chaining methods together.
     *
     * @param  array $tree
     * @param  array $config
     * @return self
     */
    public static function factory(array $tree = null, array $config = array())
    {
        return new self($tree, $config);
    }

    /**
     * Set the nav tree
     *
     * @param  array $tree
     * @return \Pop\Nav\Nav
     */
    public function setTree(array $tree = null)
    {
        $this->tree = (null !== $tree) ? $tree : array();
        return $this;
    }

    /**
     * Add to a nav tree branch
     *
     * @param  array   $branch
     * @param  boolean $prepend
     * @return \Pop\Nav\Nav
     */
    public function addBranch(array $branch, $prepend = false)
    {
        if (isset($branch['name'])) {
            $branch = array($branch);
        }
        $this->tree = ($prepend) ? array_merge($branch, $this->tree) : array_merge($this->tree, $branch);
        return $this;
    }

    /**
     * Add to a leaf to nav tree branch
     *
     * @param  string  $branch
     * @param  array   $leaf
     * @param  int     $pos
     * @param  boolean $prepend
     * @return \Pop\Nav\Nav
     */
    public function addLeaf($branch, array $leaf, $pos = null, $prepend = false)
    {
        $this->tree = $this->traverseTree($this->tree, $branch, $leaf, $pos, $prepend);
        $this->parentLevel = 1;
        $this->childLevel = 1;
        return $this;
    }

    /**
     * Set the nav tree
     *
     * @param  array $config
     * @return \Pop\Nav\Nav
     */
    public function setConfig(array $config = null)
    {
        if (null === $config) {
            $this->config = array(
                'parent' => array(
                    'node'  => 'ul'
                ),
                'child' => array(
                    'node'  => 'li'
                )
            );
        } else {
            $this->config = $config;
        }

        return $this;
    }

    /**
     * Set the Acl object
     *
     * @param  \Pop\Auth\Acl $acl
     * @return \Pop\Nav\Nav
     */
    public function setAcl(\Pop\Auth\Acl $acl = null)
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * Set the Role object
     *
     * @param  \Pop\Auth\Role $role
     * @return \Pop\Nav\Nav
     */
    public function setRole(\Pop\Auth\Role $role = null)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get the nav tree
     *
     * @return array
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * Get the config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the Acl object
     *
     * @return \Pop\Auth\Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * Get the Role object
     *
     * @return \Pop\Auth\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Build the nav object
     *
     * @return \Pop\Nav\Nav
     */
    public function build()
    {
        if (null === $this->nav) {
            $this->nav = $this->traverse($this->tree);
        }
        return $this;
    }

    /**
     * Re-build the nav object
     *
     * @return \Pop\Nav\Nav
     */
    public function rebuild()
    {
        $this->parentLevel = 1;
        $this->childLevel = 1;
        $this->nav = $this->traverse($this->tree);
        return $this;
    }

    /**
     * Get the nav object
     *
     * @return \Pop\Dom\Child
     */
    public function nav()
    {
        if (null === $this->nav) {
            $this->nav = $this->traverse($this->tree);
        }
        return $this->nav;
    }

    /**
     * Render the nav object
     *
     * @param  boolean $ret
     * @return mixed
     */
    public function render($ret = false)
    {
        if (null === $this->nav) {
            $this->nav = $this->traverse($this->tree);
        }

        if ($ret) {
            return ($this->nav->hasChildren()) ? $this->nav->render($ret) : '';
        } else {
            echo ($this->nav->hasChildren()) ? $this->nav->render($ret) : '';
        }
    }

    /**
     * Render Nav object to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render(true);
    }

    /**
     * Traverse tree to insert new leaf
     *
     * @param  array   $tree
     * @param  string  $branch
     * @param  array   $newLeaf
     * @param  int     $pos
     * @param  boolean $prepend
     * @param  int     $depth
     * @return array
     */
    protected function traverseTree($tree, $branch, $newLeaf, $pos = null, $prepend = false, $depth = 0)
    {
        $t = array();
        foreach ($tree as $leaf) {
            if (((null === $pos) || ($pos == $depth)) && ($leaf['name'] == $branch)) {
                if (isset($leaf['children'])) {
                    $leaf['children'] = ($prepend) ?
                        array_merge(array($newLeaf), $leaf['children']) : array_merge($leaf['children'], array($newLeaf));
                } else {
                    $leaf['children'] = array($newLeaf);
                }
            }
            if (isset($leaf['children'])) {
                $leaf['children'] = $this->traverseTree($leaf['children'], $branch, $newLeaf, $pos, $prepend, ($depth + 1));
            }
            $t[] = $leaf;
        }

        return $t;
    }

    /**
     * Traverse the config object
     *
     * @param  array  $tree
     * @param  int    $depth
     * @param  string $parentHref
     * @throws Exception
     * @return \Pop\Dom\Child
     */
    protected function traverse(array $tree, $depth = 1, $parentHref = null)
    {
        // Create overriding top level parent, if set
        if (($depth == 1) && isset($this->config['top'])) {
            $parent = (isset($this->config['top']) && isset($this->config['top']['node'])) ? $this->config['top']['node'] : 'ul';
            $child = (isset($this->config['child']) && isset($this->config['child']['node'])) ? $this->config['child']['node'] : 'li';

            // Create parent node
            $nav = new Child($parent);

            // Set top attributes if they exist
            if (isset($this->config['top']) && isset($this->config['top']['id'])) {
                $nav->setAttributes('id', $this->config['top']['id']);
            }
            if (isset($this->config['top']) && isset($this->config['top']['class'])) {
                $nav->setAttributes('class', $this->config['top']['class']);
            }
            if (isset($this->config['top']['attributes'])) {
                foreach ($this->config['top']['attributes'] as $attrib => $value) {
                    $nav->setAttributes($attrib, $value);
                }
            }
        } else {
            // Set up parent/child node names
            $parent = (isset($this->config['parent']) && isset($this->config['parent']['node'])) ? $this->config['parent']['node'] : 'ul';
            $child = (isset($this->config['child']) && isset($this->config['child']['node'])) ? $this->config['child']['node'] : 'li';

            // Create parent node
            $nav = new Child($parent);

            // Set parent attributes if they exist
            if (isset($this->config['parent']) && isset($this->config['parent']['id'])) {
                $nav->setAttributes('id', $this->config['parent']['id'] . '-' . $this->parentLevel);
            }
            if (isset($this->config['parent']) && isset($this->config['parent']['class'])) {
                $nav->setAttributes('class', $this->config['parent']['class'] . '-' . $depth);
            }
            if (isset($this->config['parent']['attributes'])) {
                foreach ($this->config['parent']['attributes'] as $attrib => $value) {
                    $nav->setAttributes($attrib, $value);
                }
            }
        }

        $this->parentLevel++;
        $depth++;

        // Recursively loop through the nodes
        foreach ($tree as $node) {
            $allowed = true;
            if (isset($node['acl'])) {
                if (null === $this->acl) {
                    throw new Exception('The access control object is not set.');
                }
                if (null === $this->role) {
                    throw new Exception('The current role is not set.');
                }
                $resource = (isset($node['acl']['resource'])) ? $node['acl']['resource'] : null;
                $permission = (isset($node['acl']['permission'])) ? $node['acl']['permission'] : null;
                $allowed = $this->acl->isAllowed($this->role, $resource, $permission);
            }
            if (($allowed) && isset($node['name']) && isset($node['href'])) {
                // Create child node and child link node
                $a = new Child('a', $node['name']);
                if ((substr($node['href'], 0, 1) == '/') || (substr($node['href'], 0, 4) == 'http')) {
                    $href = $node['href'];
                } else {
                    if (substr($parentHref, -1) == '/') {
                        $href = $parentHref . $node['href'];
                    } else {
                        $href = $parentHref . '/' . $node['href'];
                    }
                }

                $a->setAttributes('href', $href);
                $url = $_SERVER['REQUEST_URI'];
                if (strpos($url, '?') !== false) {
                    $url = substr($url, strpos($url, '?'));
                }

                $linkClass = null;
                if ($href == $url) {
                    if (isset($this->config['on'])) {
                        $linkClass = $this->config['on'];
                    }
                } else {
                    if (isset($this->config['off'])) {
                        $linkClass = $this->config['off'];
                    }
                }

                // If the node has any attributes
                if (isset($node['attributes'])) {
                    foreach ($node['attributes'] as $attrib => $value) {
                        $value = (($attrib == 'class') && (null !== $linkClass)) ? $value . ' ' . $linkClass : $value;
                        $a->setAttributes($attrib, $value);
                    }
                } else if (null !== $linkClass) {
                    $a->setAttributes('class', $linkClass);
                }

                $navChild = new Child($child);

                // Set child attributes if they exist
                if (isset($this->config['child']) && isset($this->config['child']['id'])) {
                    $navChild->setAttributes('id', $this->config['child']['id'] . '-' . $this->childLevel);
                }
                if (isset($this->config['child']) && isset($this->config['child']['class'])) {
                    $navChild->setAttributes('class', $this->config['child']['class'] . '-' . ($depth - 1));
                }
                if (isset($this->config['child']['attributes'])) {
                    foreach ($this->config['child']['attributes'] as $attrib => $value) {
                        $navChild->setAttributes($attrib, $value);
                    }
                }

                // Add link node
                $navChild->addChild($a);
                $this->childLevel++;

                // If there are children, loop through and add them
                if (isset($node['children']) && is_array($node['children']) && (count($node['children']) > 0)) {
                    $childrenAllowed = true;
                    // Check if the children are allowed
                    if (isset($node['acl'])) {
                        $i = 0;
                        foreach ($node['children'] as $nodeChild) {
                            if (null === $this->acl) {
                                throw new Exception('The access control object is not set.');
                            }
                            if (null === $this->role) {
                                throw new Exception('The current role is not set.');
                            }
                            $resource = (isset($nodeChild['acl']['resource'])) ? $nodeChild['acl']['resource'] : null;
                            $permission = (isset($nodeChild['acl']['permission'])) ? $nodeChild['acl']['permission'] : null;
                            if (!($this->acl->isAllowed($this->role, $resource, $permission))) {
                                $i++;
                            }
                        }
                        if ($i == count($node['children'])) {
                            $childrenAllowed = false;
                        }
                    }
                    if ($childrenAllowed) {
                        $navChild->addChild($this->traverse($node['children'], $depth, $href));
                    }
                }
                // Add child node
                $nav->addChild($navChild);
            }
        }

        return $nav;
    }

}