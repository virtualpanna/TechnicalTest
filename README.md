# Command Line PHP Script for Importing a CSV File into a PostgreSQL Database

## Documentation for Technical Test

### Development Decisions
1. I decided to use the PDO extension (pdo_pgsql) for PostgreSQL database connection since it is included in common PHP distributions and is easy to enable.
2. I used `fgets` and `str_getcsv` to parse the CSV file, as they are native PHP functions and the scope of the exercise didn't require any advanced CSV manipulation.
3. I used **Composer** as the package manager in order to use PHPUnit for automated testing. To launch PHPUnit tests, the command `composer install` needs to be executed beforehand.
4. I did not use any external libraries for the development of the script itself.

### Assumptions
List of assumptions I made while developing:

1. If the fields NAME and USERNAME are empty in the CSV file, the record will be created anyway.
2. The CSV file always has a header record that is skipped during parsing.
3. An output of the parsed CSV data is always printed to STDOUT.
4. The CREATE_TABLE directive will drop and recreate the table if it already exists, deleting all existing data.

### Improvements
List of possible improvements to my solution:

1. Database inserts are done one at a time in this version. This can work poorly on performance with a large set of records. A possible improvement could be "packing" groups of INSERT statements of a desired size in order to perform a bulk insert. This, however, will add an additional layer of complexity since the bulk insert can fail due to the UNIQUE index on the `email` field. To handle this situation, I can think of two possible solutions:
   - Check, before the bulk insert, if the email is already present and eventually exclude the duplicate record. This will consistently slow down the process, as an additional SELECT would need to be made for every record.
   - Check the result of the insert and remove the duplicate record before retrying (the better option in my opinion, since it will slow down only if the email actually already exists).
2. Objects could also be used to save data for each user; this can help with testing. Considering the nature of the exercise and that no framework is used, I opted for a plain array.
