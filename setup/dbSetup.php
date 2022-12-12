<?php
    $createDB = "CREATE DATABASE myart";

    $createTables = array();
    $createTables["users"] = "CREATE TABLE users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL,
        email VARCHAR(50) NOT NULL UNIQUE,
        password TEXT NOT NULL,
        is_owner BOOL DEFAULT 0 NOT NULL,
        avatar text NULL,
        update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

    $createTables["articles"] = "CREATE TABLE articles (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        autor_id INT(6) NOT NULL,
        content TEXT NOT NULL,
        update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";


