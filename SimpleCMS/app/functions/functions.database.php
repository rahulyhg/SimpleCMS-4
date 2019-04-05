<?php

try {
    $db_connection = new PDO("mysql:host=".DB_HOST."; dbname=".DB_NAME, DB_USER, DB_PASSWORD);

    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

function execute_sql($query, array $args) {
    global $db_connection;
    $sql = $db_connection->prepare($query);
    $sql->execute($args);
    return $sql;
}