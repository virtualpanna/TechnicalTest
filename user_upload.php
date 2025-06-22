<?php

/************************* 
 * Technical test USER_UPLOAD
 * 
 * Davide Danna
/************************* */

require "includes/directives.php";
require "includes/import.php";

$directives = handleDirectives();
$dbParams = [];

$action = isset($directives['create_table'])
    ? "build"
    : "import";

try {
    $dryRun = isset($directives['dry_run']) ? true : false;

    if (!$dryRun) {

        $dbParams = [
            "name" => "techtest_1",
            "table" => "users",
            "host" => $directives['h'],
            "user" => $directives['u'],
            "password" => $directives['p'],
        ];

        // using PDO to access DB
        $dsn = "pgsql:host=" . $dbParams['host'] . ";dbname=" . $dbParams['name'] . "";
        $pdo = new PDO($dsn, $dbParams['user'], $dbParams['password']);

        // set connection errors as exceptions
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $dbParams['pdo'] = $pdo;
    }

    switch ($action) {
        case 'build':
            echo "Building `" . $dbParams['table'] . "` table\n";
            buildeTable($dbParams);

            break;

        case 'import':
            if ($dryRun) {
                echo "Parsing CSV data, but no import... \n";
            } else {
                echo "Importing CSV data... \n";
            }

            importCsvData(
                $dbParams,
                $directives['file'],
                $dryRun
            );

            break;
    }

} catch (PDOException $e) {
    echo "DB connection failed: " . $e->getMessage() . "\n";
}
