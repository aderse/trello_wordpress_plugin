<?php

defined('ABSPATH') || exit;

function getTrelloDB() {
    $servername = getenv("DB_DL_HOST");
    $username = getenv("DB_DL_USER");
    $password = getenv("DB_DL_PASS");
    $database = getenv("DB_DL_NAME");

    $db = new mysqli($servername, $username, $password, $database);

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
    return $db;
}
