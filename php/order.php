<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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
    echo json_encode(["status" => "error", "message" => "Помилка підключення до бази даних: " . $e->getMessage()]);
    exit;
}

// Отримання JSON-даних
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Невірний формат даних (JSON)"]);
    exit;
}

// Отримуємо email з форми, якщо він є
$email = trim($data['email'] ?? '');
if (empty($email)) {
    $email = "guest@gmail.com"; // Якщо користувач не ввів email
}

// Якщо користувач не авторизований, створюємо гостьовий акаунт з його email
if (!isset($_SESSION['user'])) {
    $sqlGuest = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmtGuest = $pdo->prepare($sqlGuest);
    $stmtGuest->execute([':email' => $email]);
    $guest = $stmtGuest->fetch(PDO::FETCH_ASSOC);

    if (!$guest) {
        // Якщо гість із цим email не існує — створюємо нового
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

// Отримуємо користувача з сесії
$user = $_SESSION['user'];
$user_id = $user['id'];

// Отримання інших даних
$cart = $data['cart'] ?? null;
$shipping = $data['shipping'] ?? '';
$payment = $data['payment'] ?? '';
$addressData = $data['address'] ?? null;

// Перевірка: кошик не має бути порожнім
if (!$cart || !is_array($cart) || count($cart) === 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Відсутні обов'язкові дані (cart)"]);
    exit;
}

// Обробка адреси
$address_id = null;
if ($addressData) {
    $country = trim($addressData['country'] ?? '');
    $city = trim($addressData['city'] ?? '');
    $branch = trim($addressData['branch'] ?? '');
    
    if ($country === '' || $city === '' || $branch === '') {
        http_response_code(400);
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

// Створення замовлення в таблиці `orders`
try {
    $pdo->beginTransaction();
    
    // Вставка замовлення
    $sqlOrder = "INSERT INTO orders (user_id, total, address_id, shipping, payment)
                 VALUES (:user_id, :total, :address_id, :shipping, :payment)";
    $stmtOrder = $pdo->prepare($sqlOrder);
    $stmtOrder->execute([
        ':user_id' => $user_id,
        ':total' => $total,
        ':address_id' => $address_id,
        ':shipping' => $shipping,
        ':payment' => $payment
    ]);
    
    // Отримуємо ID останнього вставленого замовлення
    $order_id = $pdo->lastInsertId();

    // Вставка товарів у таблицю `order_items`
    foreach ($cart as $item) {
        $sqlOrderItem = "INSERT INTO order_items (order_id, weapon_id, quantity, price)
                         VALUES (:order_id, :weapon_id, :quantity, :price)";
        $stmtOrderItem = $pdo->prepare($sqlOrderItem);
        $stmtOrderItem->execute([
            ':order_id' => $order_id,
            ':weapon_id' => $item['id'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price']
        ]);
        
        // Оновлення кількості товарів
        $sqlSelect = "SELECT quantity FROM weapons WHERE id = :id";
        $stmtSelect = $pdo->prepare($sqlSelect);
        $stmtSelect->execute([':id' => $item['id']]);
        $current_quantity = $stmtSelect->fetchColumn();
        
        if ($current_quantity === false) {
            throw new Exception("Товар з id " . $item['id'] . " не знайдено.");
        }
        if ($current_quantity < $item['quantity']) {
            throw new Exception("Недостатньо товару '" . $item['name'] . "' для замовлення.");
        }

        $sqlUpdate = "UPDATE weapons SET quantity = quantity - :ordered WHERE id = :id";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':ordered' => $item['quantity'],
            ':id' => $item['id']
        ]);
    }
    
    $pdo->commit();
    
    echo json_encode(["status" => "success", "message" => "Замовлення успішно оформлено"]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Помилка оформлення замовлення: " . $e->getMessage()]);
}
?>
