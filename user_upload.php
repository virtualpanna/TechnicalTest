<?php

/************************* 
 * Technical test USER_UPLOAD
 * 
 * Davide Danna
/************************* */

require "includes/directives.php";

$db_name = "techtest_1";
$db_table = 'users';

echo "Action $action\n\n";

try {
    // using PDO to access DB
    $dsn = "pgsql:host=$db_host;dbname=$db_name";
    $pdo = new PDO($dsn, $db_user, $db_password);

    // hnadle connection errors as exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    switch($action) {
        case 'create':
            echo "Building `$db_table` table\n";
            require "includes/table.php";
            
            break;

        case 'import':
            echo "Importing data... \n";
            require "includes/import.php";

            break;
    }

} catch (PDOException $e) {
    echo "DB connection failed: " . $e->getMessage() . "\n";
}
