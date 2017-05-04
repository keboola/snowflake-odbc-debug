<?php
/**
 * Created by PhpStorm.
 * User: martinhalamicek
 * Date: 04/05/17
 * Time: 10:37
 */
set_error_handler( function ($errno, $errstr, $errfile, $errline, array $errcontext) {
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

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

function quoteIdentifier($value)
{
    $q = '"';
    return ($q . str_replace("$q", "$q$q", $value) . $q);
}

function query($odbc, $sql, array $bind = [])
{
    $stmt = odbc_prepare($odbc, $sql);
    odbc_execute($stmt, $bind);
    odbc_free_result($stmt);
}

function fetchAll($odbc, $sql, $bind = [])
{
    $stmt = odbc_prepare($odbc, $sql);
    odbc_execute($stmt, $bind);
    $rows = [];
    while ($row = odbc_fetch_array($stmt)) {
        $rows[] = $row;
    }
    odbc_free_result($stmt);
    return $rows;
}


$connection = createConnection([
    'host' => getenv('SNOWFLAKE_HOST'),
    'port' => getenv('SNOWFLAKE_PORT'),
    'database' => getenv('SNOWFLAKE_DATABASE'),
    'warehouse' => getenv('SNOWFLAKE_WAREHOUSE'),
    'user' => getenv('SNOWFLAKE_USER'),
    'password' => getenv('SNOWFLAKE_PASSWORD'),
]);

$sameConnection = createConnection([
    'host' => getenv('SNOWFLAKE_HOST'),
    'port' => getenv('SNOWFLAKE_PORT'),
    'database' => getenv('SNOWFLAKE_DATABASE'),
    'warehouse' => getenv('SNOWFLAKE_WAREHOUSE'),
    'user' => getenv('SNOWFLAKE_USER'),
    'password' => getenv('SNOWFLAKE_PASSWORD'),
]);

$anotherConnection = createConnection([
    'host' => getenv('SNOWFLAKE_2_HOST'),
    'port' => getenv('SNOWFLAKE_2_PORT'),
    'database' => getenv('SNOWFLAKE_2_DATABASE'),
    'warehouse' => getenv('SNOWFLAKE_2_WAREHOUSE'),
    'user' => getenv('SNOWFLAKE_2_USER'),
    'password' => getenv('SNOWFLAKE_2_PASSWORD'),
]);

query($connection, 'DROP SCHEMA IF EXISTS test CASCADE');
query($connection, 'CREATE SCHEMA test');
query($connection, 'CREATE TABLE test.test (name VARCHAR)');
query($connection, "INSERT INTO test.test VALUES ('abc'), ('def'), ('žížala')");

echo "\nFetch all using first connection: \n";
var_dump(fetchAll($connection, 'SELECT * FROM test.test'));

echo "\nFetch all using second connection: \n";
var_dump(fetchAll($sameConnection, 'SELECT * FROM test.test'));

echo "\nClosing first connection \n";
odbc_close($connection);

try {
    echo "\nFetch all using second connection again \n";
    var_dump(fetchAll($sameConnection, 'SELECT * FROM test.test'));
} catch (\ErrorException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\nFetch all using another connection \n";
var_dump(fetchAll($anotherConnection, 'SHOW TABLES'));


