<?php
// access CSV file in READ mode
$handle = fopen($filename, 'r');

if ($handle) {
    // skipping header
    fgets($handle);

    while (($line = fgets($handle)) !== false) {
        $data = str_getcsv($line);

        // Process the data
        print_r($data);
    }

    fclose($handle);
} else {
    echo "Error opening CSV file `filename`.";
}
