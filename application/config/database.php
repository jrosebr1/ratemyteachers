<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @ingroup core
 * @file database.php
 *
 * @brief
 * CodeIgniter database configuration file used to configure
 * how CI connects with a database.
 *
 * @author Adrian Rosebrock
 */
 
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

/**
 * Define the active group. This variable is useful when setting up
 * a development server versus a production server.
 *
 * @var $active_group
 */
$active_group = 'default';

/**
 * Define whether or not CodeIgniter will use active record.
 *
 * @var $active_record
 */
$active_record = TRUE;

/**
 * Define the host name for the development server.
 */
$db['default']['hostname'] = 'localhost';
/**
 * Define the username for the development server.
 */
$db['default']['username'] = 'your_db_user';
/**
 * Define the password for the development server.
 */
$db['default']['password'] = 'your_db_password';
/**
 * Define the database name for the development server.
 */
$db['default']['database'] = 'your_db_name';
/**
 * Define the database driver for the development server.
 */
$db['default']['dbdriver'] = 'mysql';
/**
 * Define the database prefix for the development server.
 */
$db['default']['dbprefix'] = '';
/**
 * Define whether to use persistent connections for the
 * development server.
 */
$db['default']['pconnect'] = TRUE;
/**
 * Define whether or nto to use database debugging on
 * the development server.
 */
$db['default']['db_debug'] = TRUE;
/**
 * Define whether database caching is turned on or not on
 * the development server.
 */
$db['default']['cache_on'] = FALSE;
/**
 * If caching is turned on, define the cache directory on
 * the development server.
 */
$db['default']['cachedir'] = '';
/**
 * Define the character set for the development server.
 */
$db['default']['char_set'] = 'utf8';
/**
 * Define the database collation for the development server.
 */
$db['default']['dbcollat'] = 'utf8_general_ci';
/**
 * Define the swap prefix for the development server.
 */
$db['default']['swap_pre'] = '';
/**
 * Define whether or not to automatically initialize the
 * database on the development server.
 */
$db['default']['autoinit'] = TRUE;
/**
 * Define whether or not to force strict connections.
 */
$db['default']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */