<?php
/**
 * Database connection (PDO)
 * Redgum Community Library - ICT726 Assignment 4
 *
 * Update the credentials below to match your hosting environment.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'redgum_library');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
    "mysql:host=" . DB_HOST . ";port=3307;dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
        ]
    );
} catch (PDOException $e) {
    // Never leak DB credentials or raw error details to the browser.
    error_log('Database connection failed: ' . $e->getMessage());
    die('Sorry, the website is temporarily unavailable. Please try again later.');
}
