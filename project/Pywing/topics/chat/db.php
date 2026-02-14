<?php
// db.php
header('Content-Type: application/json');

$conn = new mysqli(
    "sql307.infinityfree.com",
    "if0_40816908",
    "AACCSS2005",
    "if0_40816908_python"
);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed"
    ]);
    exit;
}

// Ensure proper encoding for chat messages
$conn->set_charset("utf8mb4");
