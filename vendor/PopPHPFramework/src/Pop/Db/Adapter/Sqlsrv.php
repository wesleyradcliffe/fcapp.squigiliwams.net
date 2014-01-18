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
 * SQLSrv Db adapter class
 *
 * @category   Pop
 * @package    Pop_Db
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Sqlsrv extends AbstractAdapter
{

    /**
     * Database
     * @var string
     */
    protected $database = null;

    /**
     * SQL statement to prepare
     * @var string
     */
    protected $sql = null;

    /**
     * Prepared statement
     * @var Resource
     */
    protected $statement = null;

    /**
     * Constructor
     *
     * Instantiate the SQLSrv database connection object.
     *
     * @param  array $options
     * @throws \Pop\Db\Adapter\Exception
     * @return \Pop\Db\Adapter\Sqlsrv
     */
    public function __construct(array $options)
    {
        if (!isset($options['database']) || !isset($options['host']) || !isset($options['username']) || !isset($options['password'])) {
            throw new Exception('Error: The proper database credentials were not passed.');
        }

        $this->connection = sqlsrv_connect(
            $options['host'],
            array(
                'Database'             => $options['database'],
                'UID'                  => $options['username'],
                'PWD'                  => $options['password'],
                'ReturnDatesAsStrings' => (isset($options['ReturnDatesAsStrings'])) ? $options['ReturnDatesAsStrings'] : true
            )
        );

        if ($this->connection == false) {
            throw new Exception('Error: Could not connect to database. ' . PHP_EOL . $this->getErrors());
        }

        $this->database = $options['database'];
    }

    /**
     * Throw an exception upon a database error.
     *
     * @throws \Pop\Db\Adapter\Exception
     * @return void
     */
    public function showError()
    {
        throw new Exception($this->getErrors());
    }

    /**
     * Get SQL errors
     *
     * @return string
     */
    public function getErrors()
    {
        $errors = null;
        $errorAry = sqlsrv_errors();

        foreach ($errorAry as $key => $value) {
            $errors .= 'SQLSTATE: ' . $value['SQLSTATE'] . ', CODE: ' . $value['code'] . ' => ' . stripslashes($value['message']) . PHP_EOL;
        }

        return $errors;
    }

    /**
     * Bind parameters to a prepared SQL query.
     *
     * @param  array  $params
     * @param  mixed  $options
     * @return \Pop\Db\Adapter\Sqlsrv
     */
    public function bindParams($params, $options = null)
    {
        $bindParams = array();
        foreach ($params as $dbColumnName => $dbColumnValue) {
            $dbColumnValueAry = (!is_array($dbColumnValue)) ? array($dbColumnValue) : $dbColumnValue;
            $i = 1;
            foreach ($dbColumnValueAry as $dbColumnValueAryValue) {
                ${$dbColumnName . $i} = $dbColumnValueAryValue;
                $bindParams[] = &${$dbColumnName . $i};
                $i++;
            }
        }

        if ((count($bindParams) > 0) && (null !== $options)) {
            $this->statement = sqlsrv_prepare($this->connection, $this->sql, $bindParams, $options);
        } else if (count($bindParams) > 0) {
            $this->statement = sqlsrv_prepare($this->connection, $this->sql, $bindParams);
        }

        return $this;
    }

    /**
     * Prepare a SQL query.
     *
     * @param  string $sql
     * @return \Pop\Db\Adapter\Sqlsrv
     */
    public function prepare($sql)
    {
        $this->sql = $sql;
        if (strpos($this->sql, '?') === false) {
            $this->statement = sqlsrv_prepare($this->connection, $sql);
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

        sqlsrv_execute($this->statement);
    }

    /**
     * Execute the SQL query and create a result resource, or display the SQL error.
     *
     * @param  string $sql
     * @return void
     */
    public function query($sql)
    {
        if (!($this->result = sqlsrv_query($this->connection, $sql))) {
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
        if (null !== $this->statement) {
            return sqlsrv_fetch_array($this->statement, SQLSRV_FETCH_ASSOC);
        } else {
            if (!isset($this->result)) {
                throw new Exception('Error: The database result resource is not currently set.');
            }

            return sqlsrv_fetch_array($this->result, SQLSRV_FETCH_ASSOC);
        }
    }

    /**
     * Return the escaped string value.
     *
     * @param  string $value
     * @return string
     */
    public function escape($value)
    {
        $search = array('\\', "\n", "\r", "\x00", "\x1a", '\'', '"');
        $replace = array('\\\\', "\\n", "\\r", "\\x00", "\\x1a", '\\\'', '\\"');

        return str_replace($search, $replace, $value);
    }

    /**
     * Return the auto-increment ID of the last query.
     *
     * @return int
     */
    public function lastId()
    {
        $this->query('SELECT SCOPE_IDENTITY() as Current_Identity');
        $row = $this->fetch();

        return (isset($row['Current_Identity'])) ? $row['Current_Identity'] : null;
    }

    /**
     * Return the number of rows in the result.
     *
     * @throws \Pop\Db\Adapter\Exception
     * @return int
     */
    public function numRows()
    {
        if (isset($this->statement)) {
            return sqlsrv_num_rows($this->statement);
        } else if (isset($this->result)) {
            return sqlsrv_num_rows($this->result);
        } else {
            throw new Exception('Error: The database result resource is not currently set.');
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
        if (isset($this->statement)) {
            return sqlsrv_num_fields($this->statement);
        } else if (isset($this->result)) {
            return sqlsrv_num_fields($this->result);
        } else {
            throw new Exception('Error: The database result resource is not currently set.');
        }
    }

    /**
     * Return the database version.
     *
     * @return string
     */
    public function version()
    {
        $server = sqlsrv_server_info($this->connection);
        return $server['SQLServerName'] . ': ' . $server['SQLServerVersion'];
    }

    /**
     * Get an array of the tables of the database.
     *
     * @return array
     */
    protected function loadTables()
    {
        $tables = array();

        $this->query("SELECT name FROM " . $this->database . "..sysobjects WHERE xtype = 'U'");
        while (($row = $this->fetch()) != false) {
            foreach($row as $value) {
                $tables[] = $value;
            }
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
        sqlsrv_close($this->connection);
    }

}
