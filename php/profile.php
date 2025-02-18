<?php
// profile.php
session_start();
session_start();
header("Access-Control-Allow-Origin: *"); // Дозволяє запити з будь-якого джерела
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Дозволяє GET, POST, OPTIONS
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Дозволяє певні заголовки
header('Content-Type: application/json');

$host   = 'localhost';           // Сервер MySQL
$dbName = 'weapon';        // Назва вашої бази даних
$user   = 'root';                // Ім'я користувача MySQL
$pass   = '';                    // Пароль до MySQL (якщо пароль відсутній, залиште порожнім)

// Перевірка, чи користувач авторизований
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Користувач не авторизований']);
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, username, email, created_at 
                       FROM users 
                       WHERE id = :id");
$stmt->bindValue(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Користувача не знайдено']);
}
