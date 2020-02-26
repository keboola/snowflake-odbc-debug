# snowflake-odbc-debug

## Demo prepartion

- Checkout this repository
- Build demo image `docker-compose build`
- Prepare `.env` file with content
```
SNOWFLAKE_HOST=XXX.snowflakecomputing.com
SNOWFLAKE_USER=DEBUG_1
SNOWFLAKE_PORT=443
SNOWFLAKE_PASSWORD=DEBUG_1_PASSWD
SNOWFLAKE_DATABASE=DEBUG_1
SNOWFLAKE_WAREHOUSE=DEBUG_1
```

### Demo execution
Run `docker-compose run --rm dev` and check the output and demo code in `test.php`
