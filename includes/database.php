<?php

/**
 * Build table `users` into database
 * 
 * Table is dropped and and rebuilt in case it already exists
 *
 * @param array $dbParams contains all data necessary for db access
 */
function buildeTable(array $dbParams): void
{
    try {
        // drop table 'users' if already exists
        $stmt = $dbParams['pdo']->prepare(query: "SELECT to_regclass(:table_name)");
        $stmt->execute(['table_name' => $dbParams['table']]);

        if ($stmt->fetchColumn()) {
            $delete_stmt = $dbParams['pdo']->prepare("DROP TABLE " . $dbParams['table']);
            $delete_stmt->execute();

            echo "Dropped existing table `" . $dbParams['table'] . "`\n";
        }

        // create table 'users'
        $sql = "CREATE TABLE users ( " .
            "name VARCHAR(50) NOT NULL, " .
            "surname VARCHAR(50) NOT NULL, " .
            "email VARCHAR(100) NOT NULL UNIQUE" .
            ")";

        $dbParams['pdo']->exec($sql);

        echo "Creation of table `" . $dbParams['table'] . "` completed successfully!\n";
    } catch (Exception $e) {
        echo "table building failed: " . $e->getMessage() . "\n";
    }
}

/**
 * Import CSV data into database
 * 
 * Before adding the records data fields coming from CSV are validated and manipulated.
 * Exceptions are handeld gracefully
 *
 * @param array $dbParams conatins all data necessary for db access
 * @param string $filename name of CSV file to be imported
 */
function importCsvData(array $dbParams, string $filename): void
{
    // access CSV file in READ mode
    $handle = fopen($filename, 'r');

    if ($handle) {
        // skipping header
        fgets($handle);

        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);

            // shows record to add
            print_r($data);

            //TODO handle dry run

            try {
                // prepare SQL statement
                $sql = "INSERT INTO users (name, surname, email) VALUES (:name, :surname, :email)";
                $stmt = $dbParams['pdo']->prepare($sql);

                // bind statement to parameters
                $stmt->bindParam(':name', $dataName);
                $stmt->bindParam(':surname', $dataSurname);
                $stmt->bindParam(':email', $dataEmail);

                // manipulating name and surname
                $dataName = ucfirst($data[0]);
                $dataSurname = ucfirst($data[1]);

                // validating email
                $dataEmail = filter_var(trim($data[2]), FILTER_VALIDATE_EMAIL)
                    ? strtolower(filter_var(trim($data[2]), FILTER_VALIDATE_EMAIL))
                    : "";

                if ($dataName && $dataSurname && $dataEmail) {
                    // insert record
                    $stmt->execute();
                    echo "New record added successfully\n";
                } else {
                    echo "Record skipped, data cannot be parsed corectly\n";
                }

            } catch (PDOException $e) {
                echo "Record could not be added:" . $e->getMessage() . "\n";
            }

            echo "\n\n";
        }

        fclose($handle);
    } else {
        echo "Error opening CSV file `filename`.";
    }
}
