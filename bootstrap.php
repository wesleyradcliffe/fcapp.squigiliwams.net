<?php

/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Loader
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * IMPORTANT!
 *
 * Require the Autoloader class file and instantiate the autoloader object.
 * If you change the relationship between this file and the framework,
 * adjust the path accordingly.
 */
require_once __DIR__ . '/vendor/PopPHPFramework/src/Pop/Loader/Autoloader.php';

$autoloader = new \Pop\Loader\Autoloader();
$autoloader->splAutoloadRegister();

use Pop\Web\Session;
$session = Session::getInstance();

//custom classes
require_once __DIR__ . '/apps/Lodestone.php';
require_once __DIR__ . '/apps/Character.php';
require_once __DIR__ . '/apps/Settings.php';
