Command line PHP script for importing into a PG database a CSV file in a specific format

## Documentation for TechnicalTest

### Assumptions
List of assumptions I used while developing:

1. while parinsg the CSV if the fields NAME and USERNAME are empty the record will be created anyway
2. the CSV file always has a header record that is skipped on parsing
3. an output of the parsed CSV data is always shown to STDOUT
4. the CREATE_TABLE directive will drop and recreate the table if already exists

### Possible Optimizations
List of possible improvements to my solution:

1. Database insert are done one at a time in this version, this can work poorly on performance on a large set of records. A possible improvement could be "packing" groups of INSERT of a desired size in order to do mass insert
2. Objects could also be user to save data for each User, this can help on testing. Considering the nature of the exerices and that no framework is used, I opted for plain array
3. Adding Unit Tests is always a good idea


I did not feel in need of using any external library for development

DD
