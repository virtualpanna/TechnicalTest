<?php

/************************* 
 * Technical test USER_UPLOAD
 * 
 * Davide Danna
/************************* */

require "includes/directives.php";
require "includes/database.php";

$directives = handleDirectives();

$dbParams = array(
    "name" => "techtest_1",
    "table" => "users",
    "host" => $directives['h'],
    "user" => $directives['u'],
    "password" => $directives['p'],
);

$action = array_key_exists("create_table", $directives)
    ? "build"
    : "import";

try {
    // using PDO to access DB
    $dsn = "pgsql:host=" . $dbParams['host'] . ";dbname=" . $dbParams['name'] . "";
    $pdo = new PDO($dsn, $dbParams['user'], $dbParams['password']);

    // set connection errors as exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbParams['pdo'] = $pdo;

    switch ($action) {
        case 'build':
            echo "Building `" . $dbParams['table'] . "` table\n";
            buildeTable($dbParams);

            break;

        case 'import':
            echo "Importing data... \n";
            importCsvData($dbParams, $directives['file']);

            break;
    }

} catch (PDOException $e) {
    echo "DB connection failed: " . $e->getMessage() . "\n";
}
