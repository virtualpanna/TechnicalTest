<?php

// access CSV file in READ mode
$handle = fopen($filename, 'r');

if ($handle) {
    // skipping header
    fgets($handle);

    while (($line = fgets($handle)) !== false) {
        $data = str_getcsv($line);

        // shows record to add
        print_r($data);

        try {
            // prepare SQL statement
            $sql = "INSERT INTO users (name, surname, email) VALUES (:name, :surname, :email)";
            $stmt = $pdo->prepare($sql);

            // bind statement to parameters
            $stmt->bindParam(':name', $dataName);
            $stmt->bindParam(':surname', $dataSurname);
            $stmt->bindParam(':email', $dataEmail);

            // manipoulating name and surname
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
