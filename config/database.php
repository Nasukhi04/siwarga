<?php
// config/database.php
require_once __DIR__ . '/app.php';

$host = 'localhost';
$dbname = 'db_siwarga';
$username = 'root';  
$password = '';       

try {
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // error jadi exception
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // hasil fetch array assoc
        PDO::ATTR_EMULATE_PREPARES   => false,                  // native prepare
    ]);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}