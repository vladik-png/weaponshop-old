<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'weapon';
$username = 'root';
$password = '';


// Дані з фронтенду
$user_id    = isset($data['user_id']) ? intval($data['user_id']) : 0;
$cart       = isset($data['cart']) ? $data['cart'] : [];
$shipping   = isset($data['shipping']) ? $data['shipping'] : '';
$payment    = isset($data['payment']) ? $data['payment'] : '';
$address_id = isset($data['address_id']) ? intval($data['address_id']) : null;

// Якщо користувач не авторизований або не передано user_id, повертаємо помилку
if ($user_id <= 0) {
    http_response_code(400);
    echo json_encode(['message' => 'Користувач не авторизований або не передано user_id.']);
    exit;
}

// Обчислюємо загальну суму замовлення
$total = 0;
foreach ($cart as $item) {
    $price = isset($item['price']) ? floatval($item['price']) : 0;
    $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
    $total += $price * $quantity;
}

// Формуємо JSON для колонки `items`
$itemsData = [
    'cart'     => $cart,
    'shipping' => $shipping,
    'payment'  => $payment
];
$itemsJson = json_encode($itemsData, JSON_UNESCAPED_UNICODE);

try {
    // Підключення до бази даних (адаптуйте параметри під ваш сервер)
    $pdo = new PDO('mysql:host=localhost;dbname=weaponshop', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Вставка даних у таблицю orders
    $sql = "INSERT INTO orders (user_id, items, total, address_id) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $itemsJson, $total, $address_id]);

    $orderId = $pdo->lastInsertId();

    echo json_encode([
        'message' => 'Замовлення успішно оформлено!',
        'order_id' => $orderId
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Помилка при оформленні замовлення.',
        'error'   => $e->getMessage()
    ]);
}
