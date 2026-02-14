<?php
$conn = new mysqli(
    "sql307.infinityfree.com", // ← use your exact DB Host
    "if0_40816908",               // DB Username
    "AACCSS2005",       // DB Password from InfinityFree
    "if0_40816908_python"         // DB Name from InfinityFree
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/* Explicit column selection (best practice) */
$sql = "
    SELECT 
        id,
        title,
        description,
        code
    FROM practical
    ORDER BY id ASC
";

$result = $conn->query($sql);

$topics = [];

while ($row = $result->fetch_assoc()) {
    $topics[] = $row;
}

header('Content-Type: application/json');
echo json_encode($topics);

$conn->close();
?>

