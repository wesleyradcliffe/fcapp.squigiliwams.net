<?php

use Pop\Db\Db,
    Pop\Db\Record;


class Settings extends Record {
	// If you want to override the table name
    // being pulled from the class name.
    // This defaults to null.
    protected $tableName = 'app_settings';

    // The table's primary ID. This defaults to 'id'
    // but can be set to null as well if there isn't
    // a primary ID for the table.
    protected $primaryId = 'name';

    // Set this to correspond to whether the
    // table is auto-incrementing or not.
    // It defaults to true.
    protected $auto = false;

}

$creds = array(
    'database' => 'fcapp',
    'host'     => 'db.squigiliwams.net',
    'username' => 'fcapp_db_connect',
    'password' => 'fcAPP1db1CONNECT208'
);

$db = Db::factory('Mysql', $creds);

Settings::setDb($db);