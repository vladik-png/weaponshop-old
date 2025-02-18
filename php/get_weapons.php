<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'weapon';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'name';
    $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

    // Запит із приєднанням категорій
    $sql = "SELECT weapons.*, categories.name AS category_name 
            FROM weapons 
            JOIN categories ON weapons.category_id = categories.id";

    // Фільтрація за категорією
    if (!empty($category)) {
        $sql .= " WHERE categories.id = :category";
    }

    // Додаємо сортування
    $sql .= " ORDER BY $sortBy $order";

    $stmt = $pdo->prepare($sql);

    if (!empty($category)) {
        $stmt->bindParam(':category', $category, PDO::PARAM_INT);
    }

    $stmt->execute();
    $weapons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($weapons);
} catch (PDOException $e) {
    echo json_encode(["error" => "Помилка підключення: " . $e->getMessage()]);
}
