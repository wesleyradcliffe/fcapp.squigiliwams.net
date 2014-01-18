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
use Pop\Code\Generator\MethodGenerator;
use Pop\Code\Generator\NamespaceGenerator;

/**
 * Form install class
 *
 * @category   Pop
 * @package    Pop_Project
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Forms
{

    /**
     * Install the form class files
     *
     * @param \Pop\Config $install
     * @return void
     */
    public static function install($install)
    {
        echo \Pop\I18n\I18n::factory()->__('Creating form class files...') . PHP_EOL;

        // Create form class folder
        $formDir = $install->project->base . '/module/' . $install->project->name . '/src/' . $install->project->name . '/Form';
        if (!file_exists($formDir)) {
            mkdir($formDir);
        }

        $forms = $install->forms->asArray();
        foreach ($forms as $name => $form) {
            $formName = ucfirst(\Pop\Filter\String::underscoreToCamelcase($name));

            // Define namespace
            $ns = new NamespaceGenerator($install->project->name . '\Form');
            $ns->setUse('Pop\Form\Form')
               ->setUse('Pop\Form\Element')
               ->setUse('Pop\Validator');

            // Create the constructor
            $construct = new MethodGenerator('__construct');
            $construct->setDesc('Constructor method to instantiate the form object');
            $construct->getDocblock()->setReturn('self');
            $construct->addArguments(
                array(
                    array('name' => 'action', 'value' => 'null',   'type' => 'string'),
                    array('name' => 'method', 'value' => "'post'", 'type' => 'string'),
                    array('name' => 'fields', 'value' => 'null',   'type' => 'array'),
                    array('name' => 'indent', 'value' => 'null',   'type' => 'string')
                )
            );

            // Create the init values array within the constructor
            if (is_array($form) && (count($form) > 0)) {
                $construct->appendToBody("\$this->initFieldsValues = array (");
                $i = 0;
                foreach ($form as $name => $field) {
                    $i++;
                    $construct->appendToBody("    '" . $name . "' => array (");
                    $j = 0;
                    foreach ($field as $key => $value) {
                        $j++;
                        $comma = ($j < count($field)) ? ',' : null;
                        if ($key == 'validators') {
                            $val = null;
                            if (is_array($value)) {
                                $val = 'array(' . PHP_EOL;
                                foreach ($value as $v) {
                                    $val .= '            new Validator\\' . $v . ',' . PHP_EOL;
                                }
                                $val .= '        )';
                            } else {
                                $val = 'new Validator\\' . $value;
                            }
                            $construct->appendToBody("        '{$key}' => {$val}{$comma}");
                        } else if (($key == 'value') || ($key == 'marked') || ($key == 'attributes') || ($key == 'error')) {
                            $val = var_export($value, true);
                            $val = str_replace(PHP_EOL, PHP_EOL . '        ', $val);
                            if (strpos($val, 'Select::') !== false) {
                                $val = 'Element\\' . str_replace("'", '', $val);
                            }
                            $construct->appendToBody("        '{$key}' => {$val}{$comma}");
                        } else {
                            if (is_bool($value)) {
                                $val = ($value) ? 'true' : 'false';
                            } else {
                                $val = "'" . $value . "'";
                            }
                            $construct->appendToBody("        '{$key}' => {$val}{$comma}");
                        }
                    }
                    $end = ($i < count($form)) ? '    ),' : '    )';
                    $construct->appendToBody($end);
                }
                $construct->appendToBody(");");
            }

            $construct->appendToBody("parent::__construct(\$action, \$method, \$fields, \$indent);");

            // Create and save form class file
            $formCls = new Generator($formDir . '/' . $formName . '.php', Generator::CREATE_CLASS);
            $formCls->setNamespace($ns);
            $formCls->code()->setParent('Form')
                            ->addMethod($construct);

            $formCls->save();
        }
    }

}
