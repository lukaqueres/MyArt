<?php

include_once( "config/web.php");
include_once( "{$GLOBALS['rootPath']}/config/db.php");
include_once( "{$GLOBALS['rootPath']}/config/auth.php");

include_once( "{$GLOBALS['rootPath']}/dependencies/helpers.php");
include_once( "{$GLOBALS['rootPath']}/dependencies/Autoloader.php" );

include_once( "{$GLOBALS['rootPath']}/dependencies/Database.php");
# include_once( "{$GLOBALS['rootPath']}/dependencies/HTMLObject.php");

use database\Database;

$db = new Database(credentials: ["servername"=>$host, "username"=>$username, "password"=>$password, "dbname"=>$db]);
$GLOBALS["database"] = $db;