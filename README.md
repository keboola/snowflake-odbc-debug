# snowflake-odbc-debug

This repository simulates connection closing using ODBC driver for snowflake.
When two or more same ODBC connections are created and one of them is closed, it closes also other connections.


## Demo prepartion

- Checkout this repository
- Download latest ODBC driver (ODBC Driver 2.12.97 - 64-bit DEB for Linux) and save it as `snowflake-odbc.deb` https://docs.snowflake.net/manuals/user-guide/odbc-download.html
- Build demo image `docker-compose build`
- Prepare Snowflake users

```SQL
## crate first user
CREATE ROLE debug_1;
CREATE DATABASE debug_1;
GRANT OWNERSHIP ON DATABASE debug_1 TO ROLE debug_1;
CREATE WAREHOUSE debug_1;
GRANT USAGE ON WAREHOUSE MARTIN_DB_IMPORT_TESTS TO ROLE debug_1;
CREATE USER debug_1
PASSWORD = 'DEBUG_1_PASSWD'
DEFAULT_ROLE = debug_1;
GRANT ROLE debug_1 TO USER debug_1;

## create second user
CREATE ROLE debug_2;
CREATE DATABASE debug_2;
GRANT OWNERSHIP ON DATABASE debug_2 TO ROLE debug_2;
CREATE WAREHOUSE debug_2;
GRANT USAGE ON WAREHOUSE MARTIN_DB_IMPORT_TESTS TO ROLE debug_2;
CREATE USER debug_2
PASSWORD = 'DEBUG_2_PASSWD'
DEFAULT_ROLE = debug_2;
GRANT ROLE debug_1 TO USER debug_2;
```

- Prepare `.env` file with content
```
SNOWFLAKE_HOST=XXX.snowflakecomputing.com
SNOWFLAKE_USER=DEBUG_1
SNOWFLAKE_PORT=443
SNOWFLAKE_PASSWORD=DEBUG_1_PASSWD
SNOWFLAKE_DATABASE=DEBUG_1
SNOWFLAKE_WAREHOUSE=DEBUG_1

SNOWFLAKE_2_HOST=XXX.snowflakecomputing.com
SNOWFLAKE_2_USER=DEBUG_2
SNOWFLAKE_2_PORT=443
SNOWFLAKE_2_PASSWORD=DEBUG_2_PASSWD
SNOWFLAKE_2_DATABASE=DEBUG_2
SNOWFLAKE_2_WAREHOUSE=DEBUG_2
```

### Demo execution
Run `docker-compose run --rm tests` and check the output and demo code https://github.com/keboola/snowflake-odbc-debug/blob/master/test.php
