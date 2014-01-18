<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Project
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Project\Install;

use Pop\Code\Generator;
use Pop\Code\Generator\PropertyGenerator;
use Pop\Code\Generator\NamespaceGenerator;

/**
 * Table install class
 *
 * @category   Pop
 * @package    Pop_Project
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Tables
{

    /**
     * Install the table class files
     *
     * @param \Pop\Config $install
     * @param array  $dbTables
     * @return void
     */
    public static function install($install, $dbTables)
    {
        echo \Pop\I18n\I18n::factory()->__('Creating database table class files...') . PHP_EOL;

        // Create table class folder
        $tableDir = $install->project->base . '/module/' . $install->project->name . '/src/' . $install->project->name . '/Table';
        if (!file_exists($tableDir)) {
            mkdir($tableDir);
        }

        // Loop through the tables, creating the classes
        foreach ($dbTables as $table => $value) {
            $prefix = (isset($value['prefix'])) ? $value['prefix'] : null;
            $tableName = ucfirst(\Pop\Filter\String::underscoreToCamelcase(str_replace($prefix, '', $table)));

            $ns = new NamespaceGenerator($install->project->name . '\Table');
            $ns->setUse('Pop\Db\Record');

            if (strpos($value['primaryId'], '|') !== false) {
                $pIdType = 'array';
                $pId = explode('|', $value['primaryId']);
            } else {
                $pIdType = 'string';
                $pId = $value['primaryId'];
            }

            if (null !== $prefix) {
                $prefix = new PropertyGenerator('prefix', 'string', $prefix, 'protected');
            }
            $propId = new PropertyGenerator('primaryId', $pIdType, $pId, 'protected');
            $propAuto = new PropertyGenerator('auto', 'boolean', $value['auto'], 'protected');

            // Create and save table class file
            $tableCls = new Generator($tableDir . '/' . $tableName . '.php', Generator::CREATE_CLASS);
            $tableCls->setNamespace($ns);
            $tableCls->code()->setParent('Record')
                             ->addProperty($propId)
                             ->addProperty($propAuto);

            if (null !== $prefix) {
                $tableCls->code()->addProperty($prefix);
            }

            $tableCls->save();
        }
    }

}
