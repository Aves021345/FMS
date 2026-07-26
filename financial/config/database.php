<?php
/**
 * Database Connection
 * financial_db - Travel and Tours Financial Management System
 *
 * Include this file wherever you need database access:
 *   require_once 'config/database.php';
 * Then use the $pdo variable to run queries.
 */

// ---- Base URL ----
// Change this if your project folder name changes. No trailing slash.
define('BASE_URL', '/financial');

// ---- Credentials ----
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307');   // custom port
define('DB_NAME', 'financial_db');
define('DB_USER', 'root');
define('DB_PASS', '');       // set your MySQL/MariaDB password here

// ---- App-wide settings (kept here so you only manage one file) ----
date_default_timezone_set('Asia/Manila');
define('APP_CURRENCY_SYMBOL', '₱');
define('APP_DATE_FORMAT', 'M d, Y');

// ---- Connect ----
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}