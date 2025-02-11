<?php
header("Access-Control-Allow-Origin: http://localhost:8080"); // Дозволяє запити з будь-якого джерела
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Дозволяє GET, POST, OPTIONS
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Дозволяє певні заголовки
header('Content-Type: application/json');
$host = 'localhost';
$dbname = 'weapon';
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
