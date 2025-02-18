<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (isset($_SESSION['user'])) {
    echo json_encode(["user" => $_SESSION['user']]);
} else {
    echo json_encode(["message" => "Користувач не авторизований"]);
}
?>
