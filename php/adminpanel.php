<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:8080"); // Дозволяємо запити з frontend
header("Access-Control-Allow-Credentials: true"); // Дозволяємо передавати cookies і сесію
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Дозволені методи
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Обробка preflight-запитів (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = 'localhost';
$dbname = 'weapon';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die(json_encode(["error" => "Помилка підключення до бази даних: " . $e->getMessage()]));
}

// Отримання списку категорій (без перевірки ролі)
if (isset($_GET['categories'])) {
    $stmt = $pdo->query("SELECT * FROM categories");
    echo json_encode($stmt->fetchAll());
    exit;
}

// Перевірка чи користувач - адміністратор
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Доступ заборонено']);
    exit;
}

// Отримання списку зброї
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT weapons.*, categories.name AS category_name FROM weapons 
                         LEFT JOIN categories ON weapons.category_id = categories.id");
    echo json_encode($stmt->fetchAll());
    exit;
}

// Додавання нової зброї
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name'], $data['description'], $data['price'], $data['quantity'], $data['image'], $data['category_id'])) {
        echo json_encode(['error' => 'Всі поля є обов’язковими']);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO weapons (name, description, price, quantity, image, category_id) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data['name'], $data['description'], $data['price'], $data['quantity'], $data['image'], $data['category_id']]);

    echo json_encode(['success' => true]);
    exit;
}

// Видалення зброї
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        echo json_encode(['error' => 'ID є обов’язковим']);
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM weapons WHERE id = ?");
    $stmt->execute([$data['id']]);
    
    echo json_encode(['success' => true]);
    exit;
}

// Оновлення зброї
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'], $data['name'], $data['description'], $data['price'], $data['quantity'], $data['image'], $data['category_id'])) {
        echo json_encode(['error' => 'Всі поля є обов’язковими']);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE weapons SET name = ?, description = ?, price = ?, quantity = ?, image = ?, category_id = ? 
                           WHERE id = ?");
    $stmt->execute([$data['name'], $data['description'], $data['price'], $data['quantity'], $data['image'], $data['category_id'], $data['id']]);
    
    echo json_encode(['success' => true]);
    exit;
}

// Якщо запит не підтримується
http_response_code(405);
echo json_encode(['error' => 'Метод не дозволений']);
