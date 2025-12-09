<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;
use App\Controllers\ReportController;

require_login();

$pdo = Database::connect();
$controller = new ReportController($pdo);
$currentUser = user();

// Only librarians can access
if ($currentUser['user_type'] !== 'librarian') {
    header('Location: index.php');
    exit;
}

$inventory_report = $controller->getInventoryReport();

include VIEWS_PATH . '/header.php';
?>
<!-- Reports dashboard for librarians -->