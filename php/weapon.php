<?php
// Дозволяємо CORS
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "weapon";

// Підключення до бази даних
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Помилка підключення: " . $conn->connect_error]));
}

// Отримуємо список зброї
$sql = "SELECT id, img AS image, name, type, descruption, price, weight, ammo FROM weapon";
$result = $conn->query($sql);

$weapons = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['descruption'] = nl2br(htmlspecialchars($row['descruption']));
        $weapons[] = $row;
    }
}

// Виводимо JSON
echo json_encode($weapons);
$conn->close();
?>
