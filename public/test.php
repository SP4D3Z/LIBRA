<?php
require_once __DIR__ . '/config.php';

echo "<h1>Configuration Test</h1>";
echo "APP_ROOT: " . APP_ROOT . "<br>";
echo "MODELS_PATH: " . MODELS_PATH . "<br>";
echo "VIEWS_PATH: " . VIEWS_PATH . "<br>";

// Test database connection
try {
    $pdo = db();
    echo "Database connection: SUCCESS<br>";
} catch (Exception $e) {
    echo "Database connection: FAILED - " . $e->getMessage() . "<br>";
}

// Test session
echo "Session status: " . session_status() . "<br>";
echo "Is logged in: " . (is_logged_in() ? 'Yes' : 'No') . "<br>";