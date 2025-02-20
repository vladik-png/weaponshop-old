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

// Отримання списку категорій з перевіркою ролі
if (isset($_GET['categories'])) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        // Для неадміністратора повертаємо порожній масив
        echo json_encode([]);
        exit;
    }
    $stmt = $pdo->query("SELECT * FROM categories");
    echo json_encode($stmt->fetchAll());
    exit;
}

// Перевірка чи користувач - адміністратор для наступних операцій
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Доступ заборонено']);
    exit;
}

// Отримання статистики
if (isset($_GET['stats'])) {
    $stats = [];

    // Кількість замовлень
    $stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM orders");
    $stats['total_orders'] = $stmt->fetch()['total_orders'];

    // Загальний обсяг продажів
    $stmt = $pdo->query("SELECT IFNULL(SUM(total),0) AS total_revenue FROM orders");
    $stats['total_revenue'] = $stmt->fetch()['total_revenue'];

    // Середня вартість замовлення
    $stmt = $pdo->query("SELECT IFNULL(AVG(total),0) AS avg_order_value FROM orders");
    $stats['avg_order_value'] = $stmt->fetch()['avg_order_value'];

    // Найпопулярніший товар (за сумою проданих одиниць)
    $stmt = $pdo->query("SELECT w.name AS weapon_name, SUM(oi.quantity) AS total_quantity 
                         FROM order_items oi 
                         JOIN weapons w ON oi.weapon_id = w.id 
                         GROUP BY oi.weapon_id 
                         ORDER BY total_quantity DESC 
                         LIMIT 1");
    $stats['most_popular_weapon'] = $stmt->fetch();

    // Кількість товарів у наявності
    $stmt = $pdo->query("SELECT COUNT(*) AS total_weapons FROM weapons");
    $stats['total_weapons'] = $stmt->fetch()['total_weapons'];

    echo json_encode($stats);
    exit;
}

// Обробка запитів для замовлень
if (isset($_GET['orders'])) {
    // Отримуємо всі замовлення з таблиці orders, приєднуючи ім'я користувача з таблиці users
    $stmt = $pdo->query("SELECT o.*, u.username AS user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id");
    $orders = $stmt->fetchAll();
    
    // Для кожного замовлення отримуємо відповідні товари з order_items та дані зброї
    foreach ($orders as &$order) {
        $stmtItems = $pdo->prepare("SELECT oi.*, w.name AS weapon_name, w.image 
                                    FROM order_items oi 
                                    LEFT JOIN weapons w ON oi.weapon_id = w.id 
                                    WHERE oi.order_id = ?");
        $stmtItems->execute([$order['id']]);
        $order['items'] = $stmtItems->fetchAll();
    }
    echo json_encode($orders);
    exit;
}

// Обробка запитів для роботи з категоріями (додавання, редагування, видалення)
if (isset($_GET['entity']) && $_GET['entity'] === 'category') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($data['name'])) {
            echo json_encode(['error' => 'Назва категорії є обов’язковою']);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$data['name']]);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        if (!isset($data['id'], $data['name'])) {
            echo json_encode(['error' => 'ID та назва категорії є обов’язковими']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['id']]);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        if (!isset($data['id'])) {
            echo json_encode(['error' => 'ID є обов’язковим']);
            exit;
        }
    
        // Перевіряємо, чи використовується категорія в товарах
        $stmt = $pdo->prepare("SELECT name FROM weapons WHERE category_id = ?");
        $stmt->execute([$data['id']]);
        $weapons = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
        if (!empty($weapons)) {
            echo json_encode(['error' => 'Категорія використовується у наступних товарах: ' . implode(', ', $weapons)]);
            exit;
        }
    
        // Якщо товарів немає – видаляємо категорію
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$data['id']]);
    
        echo json_encode(['success' => true]);
        exit;
    }
    
    http_response_code(405);
    echo json_encode(['error' => 'Метод не дозволений']);
    exit;
}

// Обробка запитів для зброї
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT weapons.*, categories.name AS category_name FROM weapons 
                         LEFT JOIN categories ON weapons.category_id = categories.id");
    echo json_encode($stmt->fetchAll());
    exit;
}

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

http_response_code(405);
echo json_encode(['error' => 'Метод не дозволений']);
?>
