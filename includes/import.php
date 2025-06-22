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

        echo "Creation of table `" . $dbParams['table'] . "` completed\n";
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
 * @param bool $dryrun flag indication weather data should be actually inserted into DB 
 */
function importCsvData(array $dbParams, string $filename, bool $dryRun): void
{
    // access CSV file in READ mode
    $handle = fopen($filename, 'r');

    if ($handle) {
        // skipping header
        fgets($handle);

        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);

            // manipulating name and surname
            $data[0] = capitalize($data[0]);
            $data[1] = capitalize($data[1]);

            // validating email
            $data[2] = filter_var(trim($data[2]), FILTER_VALIDATE_EMAIL);
            $data[2] = $data[2] ? strtolower($data[2]) : "";

            // shows record to add
            print_r($data);

            if (!$dryRun) {
                try {
                    // prepare SQL statement
                    $sql = "INSERT INTO users (name, surname, email) VALUES (:name, :surname, :email)";
                    $stmt = $dbParams['pdo']->prepare($sql);

                    // bind statement to parameters
                    $stmt->bindParam(':name', $data[0]);
                    $stmt->bindParam(':surname', $data[1]);
                    $stmt->bindParam(':email', $data[2]);

                    // insert only if email is valid
                    if ($data[2]) {
                        $stmt->execute();
                        echo "New record added successfully\n";
                    } else {
                        echo "Record skipped, data cannot be parsed corectly\n";
                    }

                } catch (PDOException $e) {
                    echo "Record could not be added:" . $e->getMessage() . "\n";
                }
            }

            echo "\n\n";
        }

        fclose($handle);
    } else {
        echo "Error opening CSV file `filename`.";
    }
}

/**
 * capitalizes specific formats strings that includes `'` characters 
 *
 * @param string $string string that should be capitalized
 */
function capitalize(string $name): string
{
    // Convert the entire string to lowercase first
    $name = strtolower($name);

    // Split the string by the apostrophe
    $parts = explode("'", $name);

    // Capitalize the first letter of each part
    foreach ($parts as &$part) {
        $part = ucfirst($part);
    }

    // Join the parts back together with the apostrophe
    return implode("'", $parts);
}
