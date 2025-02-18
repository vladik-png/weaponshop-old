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

    $term = isset($_GET['term']) ? trim($_GET['term']) : '';

    if ($term === '') {
        echo json_encode([]);
        exit;
    }

    // UNION-запит: повертаємо зброю та категорії.
    // Для зброї повертаємо її зображення (w.image), а для категорій – c.image або, якщо ні, можна повернути порожнє значення.
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

    UNION

    SELECT
      'category' AS type,
      c.id,
      c.name,
      '' AS description,
      '' AS category_name,
      '' AS image
    FROM categories c
    WHERE c.name LIKE :term
";

    $stmt = $pdo->prepare($sql);
    $likeTerm = '%' . $term . '%';
    $stmt->bindValue(':term', $likeTerm, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(["error" => "Помилка підключення: " . $e->getMessage()]);
}
