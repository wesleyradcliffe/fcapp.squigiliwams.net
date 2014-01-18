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
 * MySQLi Db adapter class
 *
 * @category   Pop
 * @package    Pop_Db
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    1.7.0
 */
class Mysqli extends AbstractAdapter
{

    /**
     * Prepared statement
     * @var \MySQLi_STMT
     */
    protected $statement = null;

    /**
     * Constructor
     *
     * Instantiate the MySQLi database connection object.
     *
     * @param  array $options
     * @throws \Pop\Db\Adapter\Exception
     * @return \Pop\Db\Adapter\Mysqli
     */
    public function __construct(array $options)
    {
        if (!isset($options['database']) || !isset($options['host']) || !isset($options['username']) || !isset($options['password'])) {
            throw new Exception('Error: The proper database credentials were not passed.');
        }

        $this->connection = new \mysqli($options['host'], $options['username'], $options['password'], $options['database']);

        if ($this->connection->connect_error != '') {
            throw new Exception('Error: Could not connect to database. Connection Error #' . $this->connection->connect_errno . ': ' . $this->connection->connect_error);
        }
    }

    /**
     * Throw an exception upon a database error.
     *
     * @throws \Pop\Db\Adapter\Exception
     * @return void
     */
    public function showError()
    {
        throw new Exception('Error: ' . $this->connection->errno . ' => ' . $this->connection->error . '.');
    }

    /**
     * Prepare a SQL query.
     *
     * @param  string $sql
     * @return \Pop\Db\Adapter\Mysqli
     */
    public function prepare($sql)
    {
        $this->statement = $this->connection->stmt_init();
        $this->statement->prepare($sql);

        return $this;
    }

    /**
     * Bind parameters to a prepared SQL query.
     *
     * @param  array  $params
     * @return \Pop\Db\Adapter\Mysqli
     */
    public function bindParams($params)
    {
        $bindParams = array('');

        foreach ($params as $dbColumnName => $dbColumnValue) {
            $dbColumnValueAry = (!is_array($dbColumnValue)) ? array($dbColumnValue) : $dbColumnValue;

            $i = 1;
            foreach ($dbColumnValueAry as $dbColumnValueAryValue) {
                ${$dbColumnName . $i} = $dbColumnValueAryValue;

                if (is_int($dbColumnValueAryValue)) {
                    $bindParams[0] .= 'i';
                } else if (is_double($dbColumnValueAryValue)) {
                    $bindParams[0] .= 'd';
                } else if (is_string($dbColumnValueAryValue)) {
                    $bindParams[0] .= 's';
                } else if (is_null($dbColumnValueAryValue)) {
                    $bindParams[0] .= 's';
                } else {
                    $bindParams[0] .= 'b';
                }

                $bindParams[] = &${$dbColumnName . $i};
                $i++;
            }
        }

        call_user_func_array(array($this->statement, 'bind_param'), $bindParams);

        return $this;
    }

    /**
     * Bind result values to variables and fetch and return the values.
     *
     * @return array
     */
    public function fetchResult()
    {
        $params = array();
        $bindParams = array();

        $metaData = $this->statement->result_metadata();

        foreach ($metaData->fetch_fields() as $col) {
            ${$col->name} = null;
            $bindParams[] = &${$col->name};
            $params[] = $col->name;
        }

        call_user_func_array(array($this->statement, 'bind_result'), $bindParams);

        $rows = array();

        while (($row = $this->statement->fetch()) != false) {
            $ary = array();
            foreach ($bindParams as $dbColumnName => $dbColumnValue) {
                $ary[$params[$dbColumnName]] = $dbColumnValue;
            }
            $rows[] = $ary;
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

        $this->statement->execute();
    }

    /**
     * Execute the SQL query and create a result resource, or display the SQL error.
     *
     * @param  string $sql
     * @return void
     */
    public function query($sql)
    {
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
        if (null !== $this->statement) {
            return $this->statement->fetch();
        } else {
            if (!isset($this->result)) {
                throw new Exception('Error: The database result resource is not currently set.');
            }

            return $this->result->fetch_array(MYSQLI_ASSOC);
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
        return $this->connection->real_escape_string($value);
    }

    /**
     * Return the auto-increment ID of the last query.
     *
     * @return int
     */
    public function lastId()
    {
        return $this->connection->insert_id;
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
            $this->statement->store_result();
            return $this->statement->num_rows;
        } else if (isset($this->result)) {
            return $this->result->num_rows;
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
            $this->statement->store_result();
            return $this->statement->field_count;
        } else if (isset($this->result)) {
            return $this->connection->field_count;
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
        return 'MySQL ' . $this->connection->server_info;
    }

    /**
     * Get an array of the tables of the database.
     *
     * @return array
     */
    protected function loadTables()
    {
        $tables = array();

        $this->query('SHOW TABLES');
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
        $this->connection->close();
    }

}
