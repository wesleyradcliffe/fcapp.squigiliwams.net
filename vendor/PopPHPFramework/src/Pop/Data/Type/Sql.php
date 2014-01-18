<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Data
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Data\Type;

/**
 * SQL data type class
 *
 * @category   Pop
 * @package    Pop_Data
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Sql
{

    /**
     * Decode the data into PHP.
     *
     * @param  string $data
     * @return mixed
     */
    public static function decode($data)
    {
        $eol = (strpos($data, "),\r\n(") !== false) ? "),\r\n(" : "),\n(";

        $fields = substr($data, (strpos($data, '(') + 1));
        $fields = substr($fields, 0, strpos($fields, ')'));
        $search = array(', ', '`', '"', "'", '[', ']');
        $replace = array(',', '', '', "", '', '');
        $fields = str_replace($search, $replace, $fields);

        $fieldsAry = explode(',', $fields);

        $valuesAry = array();
        $values = substr($data, (strpos($data, "\n") + 1));
        $insertAry = explode('INSERT', $values);
        foreach ($insertAry as $value) {
            $value = trim($value);
            if (stripos($value, 'INTO') !== false) {
                $value = trim(substr($value, (stripos($value, 'VALUES') + 6)));
            }
            $valuesAry = array_merge($valuesAry, explode($eol, $value));
        }
        $valAry = array();

        foreach ($valuesAry as $value) {
            if (substr($value, 0, 1) == '(') {
                $value = substr($value, 1);
            }
            if (substr($value, -2) == ');') {
                $value = substr($value, 0, -2);
            }

            $valAry[] = $value;
        }

        $newAry = array();
        $j = 1;

        foreach ($valAry as $val) {
            $ary = array();

            for ($i = 0; $i < count($fieldsAry); $i++) {
                if (substr($val, 0, 1) == "'") {
                    if (strpos($val, ',') !== false) {
                        $v = substr($val, 1, strpos($val, "',"));
                        $l = strlen($v) + 2;
                    } else {
                        $v = $val;
                        $l = strlen($v);
                    }
                } else {
                    if (strpos($val, ',') !== false) {
                        $v = substr($val, 0, strpos($val, ","));
                        $l = strlen($v) + 1;
                    } else {
                        $v = $val;
                        $l = strlen($v);
                    }
                }
                if (substr($v, -1) == "'") {
                    $v = substr($v, 0, -1);
                }
                if (substr($v, 0, 1) == "'") {
                    $v = substr($v, 1);
                }
                $ary[$fieldsAry[$i]] = stripslashes($v);
                $val = substr($val, $l);
                $val = ltrim($val);
            }

            $newAry['row_' . $j] = $ary;
            $j++;
        }

        return $newAry;
    }

    /**
     * Encode the data into its native format.
     *
     * @param  mixed  $data
     * @param  string $table
     * @param  string $idQuote
     * @param  int    $divide
     * @return string
     */
    public static function encode($data, $table = null, $idQuote = null, $divide = 100)
    {
        $fields = array();
        foreach ($data as $ary) {
            $fields = array_keys((array)$ary);
        }

        $table = (null === $table) ? 'data' : $table;
        $idQuoteEnd = ($idQuote == '[') ? ']' : $idQuote;
        $sql = "INSERT INTO {$idQuote}{$table}{$idQuoteEnd} ({$idQuote}" . implode("{$idQuoteEnd}, {$idQuote}", $fields). "{$idQuoteEnd}) VALUES\n";

        $i = 1;
        foreach($data as $key => $ary) {
            foreach ($ary as $k => $v) {
                $ary[$k] = "'" . str_replace("'", "\\'", $v) . "'";
            }

            $sql .= "(" . implode(', ', $ary) . ")";

            if (($i % $divide) == 0) {
                $sql .= ";\n";
                if ($i < (count($data))) {
                    $sql .= "INSERT INTO {$idQuote}{$table}{$idQuoteEnd} ({$idQuote}" . implode("{$idQuoteEnd}, {$idQuote}", $fields). "{$idQuoteEnd}) VALUES\n";
                }
            } else {
                if ($i < (count($data))) {
                    $sql .= ",\n";
                } else {
                    $sql .= ";\n";
                }
            }
            $i++;
        }

        return $sql;
    }

}
