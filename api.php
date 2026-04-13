<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM todos ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
            break;

        case 'POST':
            $title = trim($input['title'] ?? '');
            if (!$title) throw new Exception("Пустое название");
            $stmt = $pdo->prepare("INSERT INTO todos (title) VALUES (?)");
            $stmt->execute([$title]);
            echo json_encode(["id" => $pdo->lastInsertId(), "title" => $title, "is_done" => 0]);
            break;

        case 'PUT':
            $id = (int)($input['id'] ?? 0);
            $done = (int)($input['is_done'] ?? 0);
            $stmt = $pdo->prepare("UPDATE todos SET is_done = ? WHERE id = ?");
            $stmt->execute([$done, $id]);
            echo json_encode(["ok" => true]);
            break;

        case 'DELETE':
            $id = (int)($input['id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM todos WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["ok" => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Метод не поддерживается"]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
?>