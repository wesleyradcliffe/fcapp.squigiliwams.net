<?php

use Pop\Db\Db,
    Pop\Db\Record;

class Characters extends Record {
	// If you want to override the table name
    // being pulled from the class name.
    // This defaults to null.
    protected $tableName = 'characters';
}

$creds = array(
    'database' => 'fcapp',
    'host'     => 'db.squigiliwams.net',
    'username' => 'fcapp_db_connect',
    'password' => 'fcAPP1db1CONNECT208'
);

$db = Db::factory('Mysql', $creds);

Characters::setDb($db);