<?php


// drop table 'users' if already exists
$stmt = $pdo->prepare(query: "SELECT to_regclass(:table_name)");
$stmt->execute(['table_name' => $db_table]);

if ($stmt->fetchColumn()) {
    $delete_stmt = $pdo->prepare("DROP TABLE $db_table");
    $delete_stmt->execute();
    echo "Dropped existing table `$db_table`\n";

}

// create table 'users'
$sql = "CREATE TABLE users (
    name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
)";

$pdo->exec($sql);

echo "Creation of table `$db_table` completed successfully!\n";

