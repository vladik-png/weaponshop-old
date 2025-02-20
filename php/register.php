<?php
session_start();
header("Access-Control-Allow-Origin: *"); // Дозволяє запити з будь-якого джерела
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json'); // Встановлення JSON-відповіді

// Налаштування підключення до БД
$host   = 'localhost';           // Сервер MySQL
$dbName = 'weapon';        // Назва вашої бази даних
$username   = 'root';                // Ім'я користувача MySQL
$password   = '';                    // Пароль до MySQL (якщо пароль відсутній, залиште порожнім)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode([
      'success' => false, 
      'message' => "Помилка з'єднання: " . $e->getMessage()
    ]));
}

// Отримуємо дані з POST (очікуємо JSON)
$data     = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$email    = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

// Перевірка, чи всі поля заповнені
if (!$username || !$email || !$password) {
    echo json_encode([
      'success' => false, 
      'message' => 'Будь ласка, заповніть всі поля'
    ]);
    exit;
}

// Перевірка наявності користувача з таким email
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->bindValue(':email', $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo json_encode([
      'success' => false, 
      'message' => 'Користувач з таким email вже існує'
    ]);
    exit;
}

// Вставка нового користувача (паролі НЕ хешуються — для прикладу)
$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
$stmt->bindValue(':username', $username);
$stmt->bindValue(':email', $email);
$stmt->bindValue(':password', $password);

if ($stmt->execute()) {
    echo json_encode([
      'success' => true, 
      'message' => 'Реєстрація успішна'
    ]);
} else {
    $errorInfo = $stmt->errorInfo();
    echo json_encode([
      'success' => false, 
      'message' => 'Помилка при реєстрації: ' . $errorInfo[2]
    ]);
}