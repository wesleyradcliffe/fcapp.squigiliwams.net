<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Db
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Db\Sql;

/**
 * Insert SQL class
 *
 * @category   Pop
 * @package    Pop_Db
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Insert extends AbstractSql
{

    /**
     * Render the INSERT statement
     *
     * @return string
     */
    public function render()
    {
        // Start building the INSERT statement
        $sql = 'INSERT INTO ' . $this->sql->quoteId($this->sql->getTable()) . ' ';
        $columns = array();
        $values = array();

        $paramCount = 1;
        $dbType = $this->sql->getDbType();

        foreach ($this->columns as $column => $value) {
            $colValue = (strpos($column, '.') !== false) ?
                substr($column, (strpos($column, '.') + 1)) : $column;

            // Check for named parameters
            if ((':' . $colValue == substr($value, 0, strlen(':' . $colValue))) &&
                ($dbType !== \Pop\Db\Sql::SQLITE) &&
                ($dbType !== \Pop\Db\Sql::ORACLE)) {
                if (($dbType == \Pop\Db\Sql::MYSQL) || ($dbType == \Pop\Db\Sql::SQLSRV)) {
                    $value = '?';
                } else if ($dbType == \Pop\Db\Sql::PGSQL) {
                    $value = '$' . $paramCount;
                    $paramCount++;
                }
            }
            $columns[] = $this->sql->quoteId($column);
            $values[] = (null === $value) ? 'NULL' : $this->sql->quote($value);
        }

        $sql .= '(' . implode(', ', $columns) . ') VALUES ';
        $sql .= '(' . implode(', ', $values) . ')';

        return $sql;
    }

}
