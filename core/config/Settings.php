<?php

// installation (core) settings

define('SERVERENV','dev');

Settings::$LOG_FILE = $_SERVER['DOCUMENT_ROOT'].'/core/logs/'.$_SERVER['SERVER_NAME'].'.log';
Settings::$WEBROOT = $_SERVER['DOCUMENT_ROOT'].'/';// dirname(__FILE__).'/../../../';
Settings::$APPURL = 'http://'.$_SERVER['SERVER_NAME'];

require_once $_SERVER['DOCUMENT_ROOT'].'/core/config/Passwords.php';

/* READ!
 * 1. class Passwords is excluded from distribution for security reasons
 * 2. you will need to create the file Passwords.php in the same directory with Settings.php
 * 3. you will specify YOUR local read/write host, database, user and password there
 * 4. example:

<?
class Passwords {

    // development settings:
    //
    public static $dev_dbWriteHost  = 'localhost';
    public static $dev_dbReadHost   = 'localhost';
    public static $dev_dbUser       = 'my_new_user';
    public static $dev_dbPassword   = 'very_secuRe_paSSw0rd';
    public static $dev_dbName       = 'my_database_name';

    // production settings
    //
    public static $prd_dbWriteHost  = '127.0.0.1';
    public static $prd_dbReadHost   = '127.0.0.1';
    public static $prd_dbUser       = 'prod_db_user_name';
    public static $prd_dbPassword   = 'even_m0re_securE_PassW0rD';
    public static $prd_dbName       = 'prod_db_name';
}

 *
 */

class Settings {
	
    public static $OFF = 0;
    public static $OPEN_INLY_FOR = array();
    public static $RECORD_STATS = 0;
    public static $SHOW_LINKS_COMMENTS = false;      // don't show links in comments by default

    public static $DEFAULT_URL_PATH = '_____'; 			// default URL path for project CMap::$MAP
    public static $PROJECT_MODULES_PATH = '/project/modules';	// base for project modules
    public static $PROJECT_CSS_PATH = '/project/css';	// base for project CSS
    public static $PROJECT_JS_PATH = '/project/js';	// base for project JS
    public static $PROJECT_IMG_PATH = '/project/i';	// base for project images
    public static $PROJECT_TPL_PATH = '/project/tpl';	// base for project templates
    public static $CORE_MODULES_PATH = '/core';
    public static $CORE_LIB_PATH  = '/core/lib';
    public static $IMG_TMP  = '/img/tmp/';              // trailing slash is a must!
    public static $FILES_DIR  = '/files/';              // trailing slash is a must!
    public static $IMG_ROOT = '/img';

    public static $LOG_FILE;
    public static $LOG_FILE_MAX_SIZE = 5000000;
    public static $LOG_LEVEL = 0;
    public static $PROFILING = true;

    public static $dbManagerClass = "DynamicDBManager";
    public static $DB_CONNECTION_RETRIES = 5;
    public static $MAX_DB_EXEC_TIME = 2;
    
    public static $SOCKET_TIMEOUT = 10; // secs
    public static $FILE_GET_TIMEOUT = 40; // secs
    public static $PAGE_SCRAPE_MIN_IMG_SIZE = 50;
    public static $FILE_GET_LONG_TIMEOUT = 600; //secs
    public static $MAX_FILE_SIZE = 5000000; // ~5M

    public static $reCAPTCHA_PUBLIC_KEY;
    public static $reCAPTCHA_PRIVATE_KEY;

    public static $DEFAULT_LOCALE = 'ru-ru';

    public static $dbWriteHost  = '';
    public static $dbReadHost   = '';
    public static $dbUser       = '';
    public static $dbPassword   = '';
    public static $dbName       = '';

    public static $WEBROOT;
    public static $APPURL;
    public static $GA;
}

// local/development environment (as per 'hosts' file)
if (preg_match('/rospil\.org/',$_SERVER['SERVER_NAME'])) {

    Settings::$dbWriteHost  = Passwords::$dev_dbWriteHost;
    Settings::$dbReadHost   = Passwords::$dev_dbReadHost;
    Settings::$dbUser       = Passwords::$dev_dbUser;
    Settings::$dbPassword   = Passwords::$dev_dbPassword;
    Settings::$dbName       = Passwords::$dev_dbName;

// production
} else if (preg_match('/rospil\.info/',$_SERVER['SERVER_NAME'])) { // insert server name/IP later

    define('DEBUG', 0);

    Settings::$GA = ''; // Google analytics code
    
    Settings::$LOG_LEVEL = 0;
    Settings::$PROFILING = false;
 
    Settings::$dbWriteHost  = Passwords::$prd_dbWriteHost;
    Settings::$dbReadHost   = Passwords::$prd_dbReadHost;
    Settings::$dbUser       = Passwords::$prd_dbUser;
    Settings::$dbPassword   = Passwords::$prd_dbPassword;
    Settings::$dbName       = Passwords::$prd_dbName;
    
}