<?php
session_start();
session_destroy(); // Видаляємо всі дані сесії

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

header('Content-Type: application/json');
echo json_encode(["message" => "Вихід успішний"]);
?>
