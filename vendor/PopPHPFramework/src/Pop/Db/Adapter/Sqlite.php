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
namespace Pop\Db\Adapter;

/**
 * SQLite Db adapter class
 *
 * @category   Pop
 * @package    Pop_Db
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Sqlite extends AbstractAdapter
{

    /**
     * Last result
     * @var resource
     */
    protected $lastResult;

    /**
     * Last SQL query
     * @var string
     */
    protected $lastSql = null;

    /**
     * Prepared statement
     * @var \SQLite3Stmt
     */
    protected $statement = null;

    /**
     * Constructor
     *
     * Instantiate the SQLite database connection object.
     *
     * @param  array $options
     * @throws \Pop\Db\Adapter\Exception
     * @return \Pop\Db\Adapter\Sqlite
     */
    public function __construct(array $options)
    {
        // Select the DB to use, or display the SQL error.
        if (!isset($options['database'])) {
            throw new Exception('Error: The database file was not passed.');
        } else if (!file_exists($options['database'])) {
            throw new Exception('Error: The database file does not exists.');
        }

        $this->connection = new \SQLite3($options['database']);
    }

    /**
     * Throw an exception upon a database error.
     *
     * @throws \Pop\Db\Adapter\Exception
     * @return void
     */
    public function showError()
    {
        throw new Exception('Error: ' . $this->connection->lastErrorCode() . ' => ' . $this->connection->lastErrorMsg() . '.');
    }

    /**
     * Prepare a SQL query.
     *
     * @param  string $sql
     * @return \Pop\Db\Adapter\Sqlite
     */
    public function prepare($sql)
    {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }

    /**
     * Bind parameters to for a prepared SQL query.
     *
     * @param  array  $params
     * @return \Pop\Db\Adapter\Sqlite
     */
    public function bindParams($params)
    {
        foreach ($params as $dbColumnName => $dbColumnValue) {
            if (is_array($dbColumnValue)) {
                $i = 1;
                foreach ($dbColumnValue as $dbColumnVal) {
                    $dbColumnN = $dbColumnName . $i;
                    ${$dbColumnN} = $dbColumnVal;
                    $this->statement->bindParam(':' . $dbColumnN, ${$dbColumnN});
                    $i++;
                }
            } else {
                ${$dbColumnName} = $dbColumnValue;
                $this->statement->bindParam(':' . $dbColumnName, ${$dbColumnName});
            }
        }

        return $this;
    }

    /**
     * Fetch and return the values.
     *
     * @return array
     */
    public function fetchResult()
    {
        $rows = array();

        while (($row = $this->fetch()) != false) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Execute the prepared SQL query.
     *
     * @throws \Pop\Db\Adapter\Exception
     * @return void
     */
    public function execute()
    {
        if (null === $this->statement) {
            throw new Exception('Error: The database statement resource is not currently set.');
        }

        $this->result = $this->statement->execute();
    }

    /**
     * Execute the SQL query and create a result resource, or display the SQL error.
     *
     * @param  string $sql
     * @return void
     */
    public function query($sql)
    {
        if (stripos($sql, 'select') !== false) {
            $this->lastSql = $sql;
        } else {
            $this->lastSql = null;
        }

        if (!($this->result = $this->connection->query($sql))) {
            $this->showError();
        }
    }

    /**
     * Return the results array from the results resource.
     *
     * @throws \Pop\Db\Adapter\Exception
     * @return array
     */
    public function fetch()
    {
        if (!isset($this->result)) {
            throw new Exception('Error: The database result resource is not currently set.');
        }

        return $this->result->fetchArray(SQLITE3_ASSOC);
    }

    /**
     * Return the escaped string value.
     *
     * @param  string $value
     * @return string
     */
    public function escape($value)
    {
        return $this->connection->escapeString($value);
    }

    /**
     * Return the auto-increment ID of the last query.
     *
     * @return int
     */
    public function lastId()
    {
        return $this->connection->lastInsertRowID();
    }

    /**
     * Return the number of rows in the result.
     *
     * @return int
     */
    public function numRows()
    {
        if (null === $this->lastSql) {
            return $this->connection->changes();
        } else {
            if (!($this->lastResult = $this->connection->query($this->lastSql))) {
                $this->showError();
            } else {
                $num = 0;
                while (($row = $this->lastResult->fetcharray(SQLITE3_ASSOC)) != false) {
                    $num++;
                }
                return $num;
            }
        }
    }

    /**
     * Return the number of fields in the result.
     *
     * @throws \Pop\Db\Adapter\Exception
     * @return int
     */
    public function numFields()
    {
        if (!isset($this->result)) {
            throw new Exception('Error: The database result resource is not currently set.');
        }

        return $this->result->numColumns();
    }

    /**
     * Return the database version.
     *
     * @return string
     */
    public function version()
    {
        $ver = $this->connection->version();
        return 'SQLite ' . $ver['versionString'];
    }

    /**
     * Get an array of the tables of the database.
     *
     * @return array
     */
    protected function loadTables()
    {
        $tables = array();
        $sql = "SELECT name FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%' UNION ALL SELECT name FROM sqlite_temp_master WHERE type IN ('table', 'view') ORDER BY 1";

        $this->query($sql);
        while (($row = $this->fetch()) != false) {
            $tables[] = $row['name'];
        }

        return $tables;
    }

    /**
     * Close the DB connection.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->connection->close();
    }

}
