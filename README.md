Command line PHP script for importing into a PostgreSQL database a CSV file in a specific format

## Documentation for TechnicalTest

### Development decisions
1. I decided to use PDO extension (pdo_pgsql) for PostgreSQL database connection, since it is included in common php distribution and it's easy to enable
2. I used `fgets` and `str_getcsv` to parse the CSV file, since they are native PHP functions and the scope of the exercise didn;t need any advanced CSV manipulation
3. I used **composer** as package manager in order to use PhpUnit for Automated Testing, to launch PHPUnit tests the command `composer install` needs to be executed beforehand

### Assumptions
List of assumptions I used while developing:

1. If the fields NAME and USERNAME are empty in the CSV file the record will be created anyway
2. the CSV file always has a header record that is skipped on parsing
3. an output of the parsed CSV data is always printed to STDOUT
4. the CREATE_TABLE directive will drop and recreate the table if already exists, deleting all existing data

### Improvements
List of possible improvements to my solution:

1. Database insert are done one at a time in this version, this can work poorly on performance on a large set of records. A possible improvement could be "packing" groups of INSERT of a desired size in order to do a bulk insert. This though will add an additional layer of complexity since the bulk insert can fail due to the UNIQUE index on `email` field, to handle this situation I can think of 2 possible solutions:
- check, before the bulk insert, if the email is already present and eventually exclude the duplicate record. This will slow down consistently the process though, since an additional SELECT should me made for evey record
- check the result of the insert and remove the duplicate record before retrying (better option in my opinion, since it will slow down only if the email actually alrady exists)
2. Objects could also be user to save data for each User, this can help on testing. Considering the nature of the exerices and that no framework is used, I opted for plain array

DD
