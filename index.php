<?php
    # session_start();

    require "config/web.php";
	require "{$GLOBALS['rootPath']}/config/db.php";
	require "{$GLOBALS['rootPath']}/config/auth.php";
    require "{$GLOBALS['rootPath']}/dependencies/Autoloader.php";

    Dependencies\Autoloader::register();
    new Dependencies\Session();

    $GLOBALS["database"] = new Dependencies\Database(credentials: ["servername"=>$host, "username"=>$username, "password"=>$password, "dbname"=>$db]);
    $GLOBALS["request"] = new Web\Models\Request();
    if (Dependencies\Session::user()) {
        Dependencies\Session::user()->refresh();
    }

    require "{$GLOBALS['rootPath']}/web/web.php";