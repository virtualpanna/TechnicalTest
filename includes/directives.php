<?php 

$shortopts = "u:";      // value required
$shortopts .= "p:";     // value required
$shortopts .= "h:";     // value required

$longopts  = array(
    "file:",            // value required
    "create_table",     // no value accepted
    "dry_run",          // no value accepted
    "help",             // no value accepted
);

$options = getopt($shortopts, $longopts);

// HELP directive
if (array_key_exists("help", $options)) {
    echo "user_upload usage help:\n\n";
    echo "--create_table: this will cause the PostgreSQL users table to be built (and no further action will be taken).\n";
    echo "--dry_run: this will be used with the --file directive in case we want to run the script but not insert into the database. All other functions will be executed, but the database won't be altered.\n";
    echo "-u: PostgreSQL username.\n";
    echo "-p: PostgreSQL password.\n";
    echo "-h: PostgreSQL host.\n";
    echo "--help: usage help.\n\n";

    exit();
}

// check DB parameters for FILE and CREATE_TABLE directive
if (
    (
        array_key_exists("create_table", $options) ||
        array_key_exists("file", $options)
    ) && 
    !array_key_exists("dry_run", $options)
 ) {
    if( !array_key_exists("u", $options) ||
        !array_key_exists("p", $options) ||
        !array_key_exists("h", $options)
    ) {
        var_dump($options);
        exit("Input error: parameters -u, -p and -h must be provided\n\n");
    } else {
        $action = array_key_exists("create_table", $options) ? "create" : "import";

        $db_host = $options['h'];
        $db_user = $options['u'];
        $db_password = $options['p'];
    }
}

// check CSV presence and existance
if (array_key_exists("file", $options)) {
    if( !file_exists($options["file"])) {
        exit("Input error: parameter --file, CSV file not found\n\n");
    }
    $filename = $options["file"];
} elseif(!array_key_exists("create_table", $options)) {
    exit("Input error: parameter --file must be provided\n\n");
}
