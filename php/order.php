<?php
header("Content-Type: application/json");
require_once "db.php"; // Файл підключення до бази даних

session_start();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Отримати адресу користувача
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "Користувач не авторизований"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, country, city, branch FROM addresses WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $address = $result->fetch_assoc();
        echo json_encode(["address" => $address]);
    } else {
        echo json_encode(["error" => "Адреса не знайдена"]);
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["user_id"], $data["cart"], $data["address_id"])) {
        echo json_encode(["error" => "Некоректні дані"]);
        exit;
    }

    $user_id = intval($data["user_id"]);
    $cart = json_encode($data["cart"]);
    $total = array_reduce($data["cart"], function ($sum, $item) {
        return $sum + ($item["price"] * $item["quantity"]);
    }, 0);
    $address_id = intval($data["address_id"]);

    $stmt = $conn->prepare("INSERT INTO orders (user_id, items, total, address_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isdi", $user_id, $cart, $total, $address_id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Замовлення успішно створене"]);
    } else {
        echo json_encode(["error" => "Помилка при створенні замовлення"]);
    }
    exit;
}

echo json_encode(["error" => "Непідтримуваний запит"]);
