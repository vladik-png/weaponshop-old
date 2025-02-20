<?php
// Вимикаємо показ помилок для чистоти відповіді
error_reporting(0);
ini_set('display_errors', 0);

// Запускаємо буфер виводу та сесію
ob_start();
session_start();

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = 'localhost';
$dbname = 'weapon';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Помилка підключення до бази даних: " . $e->getMessage()]);
    exit;
}

// Обробка GET-запиту – повернення замовлень користувача разом із товарами
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Неавторизований користувач"]);
        exit;
    }
    
    $user = $_SESSION['user'];
    $user_id = $user['id'];
    
    // Отримуємо замовлення користувача з таблиці orders
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Для кожного замовлення отримуємо відповідні товари з таблиці order_items та дані про зброю
    foreach ($orders as &$order) {
        $stmtItems = $pdo->prepare("
            SELECT oi.*, w.name, w.image 
            FROM order_items oi 
            LEFT JOIN weapons w ON oi.weapon_id = w.id 
            WHERE oi.order_id = ?
        ");
        $stmtItems->execute([$order['id']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        $order['items'] = $items;
    }
    
    ob_clean();
    echo json_encode(["status" => "success", "orders" => $orders]);
    exit;
}

// Обробка POST-запиту – оформлення замовлення
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        http_response_code(400);
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Невірний формат даних (JSON)"]);
        exit;
    }
    
    // Отримання email (якщо не вказано – guest)
    $email = trim($data['email'] ?? '');
    if (empty($email)) {
        $email = "guest@example.com";
    }
    
    // Якщо користувач не авторизований, створюємо/використовуємо гостьовий акаунт
    if (!isset($_SESSION['user'])) {
        $sqlGuest = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmtGuest = $pdo->prepare($sqlGuest);
        $stmtGuest->execute([':email' => $email]);
        $guest = $stmtGuest->fetch(PDO::FETCH_ASSOC);
    
        if (!$guest) {
            $sqlInsertGuest = "INSERT INTO users (username, email, password, role) VALUES ('guest', :email, '', 'guest')";
            $stmtInsertGuest = $pdo->prepare($sqlInsertGuest);
            $stmtInsertGuest->execute([':email' => $email]);
            $guest_id = $pdo->lastInsertId();
            $guest = [
                'id' => $guest_id,
                'username' => 'guest',
                'email' => $email,
                'role' => 'guest'
            ];
        }
    
        $_SESSION['user'] = $guest;
    }
    
    $user = $_SESSION['user'];
    $user_id = $user['id'];
    
    // Отримання даних замовлення
    $cart = $data['cart'] ?? null;
    $shipping = $data['shipping'] ?? '';
    $payment = $data['payment'] ?? '';
    $addressData = $data['address'] ?? null;
    
    if (!$cart || !is_array($cart) || count($cart) === 0) {
        http_response_code(400);
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Відсутні обов'язкові дані (cart)"]);
        exit;
    }
    
    // Обробка даних адреси (якщо вказана)
    $address_id = null;
    if ($addressData) {
        $country = trim($addressData['country'] ?? '');
        $city = trim($addressData['city'] ?? '');
        $branch = trim($addressData['branch'] ?? '');
        
        if ($country === '' || $city === '' || $branch === '') {
            http_response_code(400);
            ob_clean();
            echo json_encode(["status" => "error", "message" => "Будь ласка, заповніть всі поля адреси (країна, місто, відділення)"]);
            exit;
        }
        
        $sqlAddress = "INSERT INTO addresses (user_id, country, city, branch, phone_number) 
                       VALUES (:user_id, :country, :city, :branch, :phone_number)";
        $stmtAddress = $pdo->prepare($sqlAddress);
        $phone_number = trim($data['phone'] ?? '');
        $stmtAddress->execute([
            ':user_id' => $user_id,
            ':country' => $country,
            ':city' => $city,
            ':branch' => $branch,
            ':phone_number' => $phone_number
        ]);
        $address_id = $pdo->lastInsertId();
    }
    
    // Обчислення загальної суми замовлення
    $total = 0;
    foreach ($cart as $item) {
        if (isset($item['price']) && isset($item['quantity'])) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Вставка замовлення в таблицю orders
        $sqlOrder = "INSERT INTO orders (user_id, total, shipping, payment, address_id) VALUES (:user_id, :total, :shipping, :payment, :address_id)";
        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->execute([
            ':user_id' => $user_id,
            ':total' => $total,
            ':shipping' => $shipping,
            ':payment' => $payment,
            ':address_id' => $address_id
        ]);
        $order_id = $pdo->lastInsertId();
        
        // Для кожного товару з кошика: перевірка кількості, вставка в order_items та оновлення залишку в weapons
        foreach ($cart as $item) {
            $stmtSelect = $pdo->prepare("SELECT quantity FROM weapons WHERE id = :id");
            $stmtSelect->execute([':id' => $item['id']]);
            $current_quantity = $stmtSelect->fetchColumn();
            
            if ($current_quantity === false) {
                throw new Exception("Товар з id " . $item['id'] . " не знайдено.");
            }
            if ($current_quantity < $item['quantity']) {
                throw new Exception("Недостатньо товару '" . $item['name'] . "' для замовлення.");
            }
            
            $sqlInsertItem = "INSERT INTO order_items (order_id, weapon_id, quantity, price) VALUES (:order_id, :weapon_id, :quantity, :price)";
            $stmtInsertItem = $pdo->prepare($sqlInsertItem);
            $stmtInsertItem->execute([
                ':order_id' => $order_id,
                ':weapon_id' => $item['id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
            
            $sqlUpdate = "UPDATE weapons SET quantity = quantity - :ordered WHERE id = :id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':ordered' => $item['quantity'],
                ':id' => $item['id']
            ]);
        }
        
        $pdo->commit();
        ob_clean();
        echo json_encode(["status" => "success", "message" => "Замовлення успішно оформлено"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        ob_clean();
        echo json_encode(["status" => "error", "message" => "Помилка оформлення замовлення: " . $e->getMessage()]);
    }
}
?>
