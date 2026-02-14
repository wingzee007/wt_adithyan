<?php
header("Content-Type: application/json");
session_start();

require_once __DIR__ . "/db.php";

$userId = $_SESSION['user_id'] ?? 1;

if (!$conn || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "history" => [],
        "error" => "Database connection failed"
    ]);
    exit;
}

$history = [];

$stmt = $conn->prepare(
    "SELECT question FROM user_questions 
     WHERE user_id = ? 
     ORDER BY id DESC 
     LIMIT 30"
);

if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $history[] = $row['question'];
    }

    $stmt->close();
}

$conn->close();

echo json_encode([
    "success" => true,
    "history" => $history
]);
