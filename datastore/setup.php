<?php

$db_conn = require __DIR__.'/../utils/setup_database_connection.php';

try {
    $conn->exec("CREATE TABLE IF NOT EXISTS sold_tickets(
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        address VARCHAR(50) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        quantity INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

    $conn->exec("CREATE TABLE IF NOT EXISTS ticket_purchase_sessions(
        session_id VARCHAR(128) PRIMARY KEY NOT NULL,
        quantity INT NOT NULL,
        start_time INT NOT NULL) WITHOUT ROWID");

    echo "Database Setup Success: " . $dsn . PHP_EOL;
} catch (PDOException $e) {
    die("Database Setup Error: " . $e->getMessage());
}
