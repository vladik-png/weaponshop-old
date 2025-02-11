<?php
header('Content-Type: application/json');
$host = 'localhost';
$dbname = 'weapons_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM weapons");
    $weapons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($weapons);
} catch (PDOException $e) {
    echo json_encode(["error" => "Помилка підключення: " . $e->getMessage()]);
}
