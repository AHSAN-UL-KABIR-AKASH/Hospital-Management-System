<?php
/**
 * Database connection (PDO).
 * Keeps DB access separate from application logic so future
 * mobile/PC clients can reuse the same PHP API layer.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'cancer_care');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Base site settings
define('SITE_NAME', 'Cancer Care');
define('BASE_URL', '/cancer-care/');
define('CURRENCY_SYMBOL', '৳');

// Upload directories (relative to project root)
define('UPLOAD_PATIENTS_DIR', __DIR__ . '/../uploads/patients/');
define('UPLOAD_DOCUMENTS_DIR', __DIR__ . '/../uploads/documents/');
define('UPLOAD_PATIENTS_URL', BASE_URL . 'uploads/patients/');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die('Database connection failed. Please check config/database.php and ensure MySQL is running in XAMPP.');
        }
    }
    return $pdo;
}
