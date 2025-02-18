<?php
session_start();

header("Access-Control-Allow-Origin: *"); // Дозволяє запити з будь-якого джерела
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

    // Отримуємо параметри фільтрації та сортування з GET-запиту
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'name';
    $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

    // Формуємо SQL запит з урахуванням фільтрації та сортування
    $sql = "SELECT * FROM weapons";

    // Фільтрація за категорією
    if ($category) {
        $sql .= " WHERE category = :category";
    }

    // Сортування
    $sql .= " ORDER BY $sortBy $order";

    // Підготовка та виконання запиту
    $stmt = $pdo->prepare($sql);

    // Прив'язуємо параметри для фільтрації
    if ($category) {
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
    }

    $stmt->execute();

    // Отримуємо результати запиту
    $weapons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Відправляємо відповідь у форматі JSON
    echo json_encode($weapons);

} catch (PDOException $e) {
    echo json_encode(["error" => "Помилка підключення: " . $e->getMessage()]);
}
