<?php
// config.php - Bootstrap file for path configuration
define('APP_ROOT', dirname(__DIR__));
define('MODELS_PATH', APP_ROOT . '/app/Models');
define('CONTROLLERS_PATH', APP_ROOT . '/app/Controllers');
define('VIEWS_PATH', APP_ROOT . '/app/Views');
define('HELPERS_PATH', APP_ROOT . '/app/Helpers');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload classes (PSR-4 style)
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    
    // Try to find the class in different directories
    $paths = [
        APP_ROOT . '/app/' . $class . '.php',
        APP_ROOT . '/' . $class . '.php',
        __DIR__ . '/../app/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Helper functions
function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function user() {
    return $_SESSION['user'] ?? null;
}

// Database connection shortcut
function db() {
    static $pdo = null;
    if ($pdo === null) {
        require_once APP_ROOT . '/app/Models/Database.php';
        $pdo = \App\Models\Database::connect();
    }
    return $pdo;
}