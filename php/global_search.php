<?php
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$host = 'localhost';
$dbname = 'weapon';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Отримання пошукового запиту з параметра "term"
    $term = isset($_GET['term']) ? trim($_GET['term']) : '';
    if ($term === '') {
        echo json_encode([]);
        exit;
    }

    // Режим роботи: suggestion (підказки) або detail (детальний пошук)
    $mode = isset($_GET['mode']) ? $_GET['mode'] : 'suggestion';

    if ($mode === 'suggestion') {
        // Об’єднаний запит: повертаємо лише зброю
        $sql = "
            SELECT
              'weapon' AS type,
              w.id,
              w.name,
              w.description,
              c.name AS category_name,
              w.image AS image
            FROM weapons w
            JOIN categories c ON w.category_id = c.id
            WHERE w.name LIKE :term OR w.description LIKE :term
        ";
        $stmt = $pdo->prepare($sql);
        $likeTerm = '%' . $term . '%';
        $stmt->bindValue(':term', $likeTerm, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } elseif ($mode === 'detail') {
        // Детальний запит: повертаємо лише товари із зброї з додатковими даними
        $sql = "
            SELECT weapons.*, categories.name AS category_name 
            FROM weapons 
            JOIN categories ON weapons.category_id = categories.id
            WHERE weapons.name LIKE :term OR weapons.description LIKE :term
        ";
        $stmt = $pdo->prepare($sql);
        $likeTerm = '%' . $term . '%';
        $stmt->bindValue(':term', $likeTerm, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Помилка підключення: " . $e->getMessage()]);
}
?>
