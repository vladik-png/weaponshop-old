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

    // Якщо це POST-запит, обробляємо оновлення кількості товару
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Отримуємо дані з тіла запиту
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id']) && isset($data['quantity'])) {
            // Оновлюємо кількість для конкретного товару
            $sql = "UPDATE weapons SET quantity = :quantity WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':quantity' => $data['quantity'],
                ':id'       => $data['id']
            ]);
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['error' => 'Невірні дані']);
            exit;
        }
    } else {
        // Якщо це GET-запит, повертаємо список зброї з категоріями
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $sortBy   = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'name';
        $order    = isset($_GET['order']) ? $_GET['order'] : 'ASC';

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
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Помилка підключення: " . $e->getMessage()]);
}
?>
