<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Перевірка авторизації користувача
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Користувач не авторизований.'
    ]);
    exit;
}

$user_id = $_SESSION['user']['id'];

// Підключення до бази даних
$host        = 'localhost';
$dbname      = 'weapon';
$db_username = 'root';
$db_password = '';
$mysqli = new mysqli($host, $db_username, $db_password, $dbname);

if ($mysqli->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => "Помилка з'єднання з базою даних: " . $mysqli->connect_error
    ]);
    exit;
}

// Функція для отримання останньої адреси користувача
function getLatestAddress($mysqli, $user_id) {
    $stmt = $mysqli->prepare("SELECT country, city, branch, phone_number FROM addresses WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $addressData = $result->fetch_assoc();
    $stmt->close();
    return $addressData ? $addressData : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Отримуємо останню збережену адресу користувача
    $addressData = getLatestAddress($mysqli, $user_id);
    if ($addressData !== null) {
        echo json_encode([
            'success' => true,
            'address' => $addressData
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'address' => null,
            'message' => 'Адреса не вказана.'
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Зчитування даних з POST-запиту (формат JSON)
    $input = json_decode(file_get_contents('php://input'), true);
    if (
        !isset($input['country']) || empty(trim($input['country'])) ||
        !isset($input['city'])    || empty(trim($input['city'])) ||
        !isset($input['branch'])  || empty(trim($input['branch'])) ||
        !isset($input['phone_number']) || empty(trim($input['phone_number']))
    ) {
        echo json_encode([
            'success' => false,
            'message' => 'Не всі поля адреси або номер телефону заповнені.'
        ]);
        exit;
    }

    $country = trim($input['country']);
    $city    = trim($input['city']);
    $branch  = trim($input['branch']);
    $phone_number = trim($input['phone_number']);

    // Перевірка, чи вже існує запис адреси для цього користувача
    $stmt = $mysqli->prepare("SELECT id FROM addresses WHERE user_id = ?");
    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Помилка підготовки запиту: ' . $mysqli->error
        ]);
        exit;
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Запис існує – оновлюємо його
        $stmt->close();
        $stmt = $mysqli->prepare("UPDATE addresses SET country = ?, city = ?, branch = ?, phone_number = ? WHERE user_id = ?");
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'Помилка підготовки запиту оновлення: ' . $mysqli->error
            ]);
            exit;
        }
        $stmt->bind_param("ssssi", $country, $city, $branch, $phone_number, $user_id);
        if ($stmt->execute()) {
            $stmt->close();
            $updatedAddress = getLatestAddress($mysqli, $user_id);
            echo json_encode([
                'success' => true,
                'message' => 'Адресу та номер телефону оновлено.',
                'address' => $updatedAddress
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Не вдалося оновити адресу: ' . $stmt->error
            ]);
        }
    } else {
        // Запису немає – вставляємо новий запис
        $stmt->close();
        $stmt = $mysqli->prepare("INSERT INTO addresses (user_id, country, city, branch, phone_number) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'Помилка підготовки запиту вставки: ' . $mysqli->error
            ]);
            exit;
        }
        $stmt->bind_param("issss", $user_id, $country, $city, $branch, $phone_number);
        if ($stmt->execute()) {
            $stmt->close();
            $updatedAddress = getLatestAddress($mysqli, $user_id);
            echo json_encode([
                'success' => true,
                'message' => 'Адресу та номер телефону збережено.',
                'address' => $updatedAddress
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Не вдалося зберегти адресу: ' . $stmt->error
            ]);
        }
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Непідтримуваний метод запиту.'
    ]);
}

$mysqli->close();
?>