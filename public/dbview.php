<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;

$pdo = Database::connect();

echo "<h1>Database: smart_library</h1>";

// Show all tables
$tables = $pdo->query("SHOW TABLES")->fetchAll();
echo "<h3>Tables:</h3><ul>";
foreach($tables as $table) {
    $tableName = $table[0];
    echo "<li><strong>$tableName</strong>";
    
    // Show column info
    $columns = $pdo->query("DESCRIBE $tableName")->fetchAll();
    echo "<table border='1' style='margin:10px;'>";
    foreach($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Key']}</td></tr>";
    }
    echo "</table>";
    
    // Show row count
    $count = $pdo->query("SELECT COUNT(*) FROM $tableName")->fetchColumn();
    echo "Rows: $count<br><br>";
    
    echo "</li>";
}
echo "</ul>";