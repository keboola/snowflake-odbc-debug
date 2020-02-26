<?php
/**
 * Created by PhpStorm.
 * User: martinhalamicek
 * Date: 04/05/17
 * Time: 10:37
 */
set_error_handler(
    function ($errno, $errstr, $errfile, $errline, array $errcontext) {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
);

function quoteIdentifier($value)
{
    $q = '"';
    return ($q . str_replace("$q", "$q$q", $value) . $q);
}

function createConnection(array $options)
{
    $dsn = "Driver=SnowflakeDSIIDriver;Server=" . $options['host'];

    if (isset($options['database'])) {
        $dsn .= ";Database=" . quoteIdentifier($options['database']);
    }

    if (isset($options['warehouse'])) {
        $dsn .= ";Warehouse=" . quoteIdentifier($options['warehouse']);
    }

    $dsn .= ';Tracing=6';

    echo $dsn . "\n";
    return odbc_connect($dsn, $options['user'], $options['password']);
}

$db = createConnection(
    [
        'host' => getenv('SNOWFLAKE_HOST'),
        'port' => getenv('SNOWFLAKE_PORT'),
        'database' => getenv('SNOWFLAKE_DATABASE'),
        'warehouse' => getenv('SNOWFLAKE_WAREHOUSE'),
        'user' => getenv('SNOWFLAKE_USER'),
        'password' => getenv('SNOWFLAKE_PASSWORD'),
    ]
);

function db_exec($db, $query) {
    echo 'odbc_exec(' . $query . ')' . PHP_EOL . PHP_EOL;
    odbc_exec($db, $query);
}

function db_prepare($db, $query) {
    echo 'odbc_prepare(' . $query . ')' . PHP_EOL . PHP_EOL;
    odbc_prepare($db, $query);
}

db_exec($db, 'CREATE SCHEMA IF NOT EXISTS "TF-KBC-227"');

db_exec($db, 'DROP TABLE IF EXISTS "TF-KBC-227"."TEST_TABLE"');

db_prepare($db, 'CREATE TABLE "TF-KBC-227"."TEST_TABLE" (col1 varchar, col2 varchar)');

echo 'expectation: odbc_prepare only prepared the query it did not execut, so preparing again should not fail' .
    PHP_EOL . PHP_EOL;

db_prepare($db, 'CREATE TABLE "TF-KBC-227"."TEST_TABLE" (col1 varchar, col2 varchar)');
