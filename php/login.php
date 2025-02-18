<?php
session_start();
header("Access-Control-Allow-Origin: *"); // Дозволяє запити з будь-якого джерела
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Дозволяє GET, POST, OPTIONS
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Дозволяє певні заголовки
header('Content-Type: application/json');

// Налаштування підключення до БД
$host   = 'localhost';           // Сервер MySQL
$dbName = 'weapon';        // Назва вашої бази даних
$user   = 'root';                // Ім'я користувача MySQL
$pass   = '';                    // Пароль до MySQL (якщо пароль відсутній, залиште порожнім)

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
$stmt->execute([':email' => $email, ':password' => $password]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Зберігаємо дані в сесії
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role']; // Ось тут фіксуємо роль

    echo json_encode([
        'success' => true,
        'message' => 'Вхід успішний',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Неправильний email або пароль']);
}