<?php
/**
 * Database Configuration File
 * 
 * This file contains database connection settings.
 * Update these values according to your hosting environment.
 * 
 * For Alwaysdata or production:
 * - DB_HOST: usually 'mysql-yourname.alwaysdata.net' or 'localhost'
 * - DB_NAME: your database name
 * - DB_USER: your database username
 * - DB_PASS: your database password
 */

// Database configuration constants (Alwaysdata)
// Database configuration constants (Alwaysdata)
define('DB_HOST', 'mysql-valiyyadinaliyev.alwaysdata.net');  // Alwaysdata MySQL host
define('DB_NAME', '443293_student');          // Your DB name
define('DB_USER', 'student');                        // Your DB user
define('DB_PASS', 'baki1234');                               // Your DB password
define('DB_CHARSET', 'utf8mb4');
// Character set

/**
 * Get PDO database connection
 * 
 * @return PDO Database connection object
 * @throws PDOException if connection fails
 */
function getDbConnection() {
    static $pdo = null;
    
    // Return existing connection if already established
    if ($pdo !== null) {
        return $pdo;
    }
    
    try {
        // Create DSN (Data Source Name)
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        // PDO options for better security and error handling
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                   // Use real prepared statements
            PDO::ATTR_PERSISTENT         => false                     // Don't use persistent connections
        ];
        
        // Create PDO instance
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Log error (in production, log to file instead of displaying)
        error_log("Database Connection Error: " . $e->getMessage());
        
        // Display user-friendly error
        die("Database connection failed. Please check your configuration.");
    }
}

/**
 * Test database connection
 * 
 * @return bool True if connection successful
 */
function testDbConnection() {
    try {
        $pdo = getDbConnection();
        return $pdo !== null;
    } catch (Exception $e) {
        return false;
    }
}
