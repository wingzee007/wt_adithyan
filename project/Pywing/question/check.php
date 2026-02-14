
<?php
$conn = new mysqli(
    "sql307.infinityfree.com", // ← use your exact DB Host
    "if0_40816908",               // DB Username
    "AACCSS2005",       // DB Password from InfinityFree
    "if0_40816908_python"         // DB Name from InfinityFree
);

// Validate input
if (!isset($_POST['id'], $_POST['user_answer'])) {
    echo "invalid||";
    exit;
}

$id = intval($_POST['id']);
$user = $_POST['user_answer']; // DO NOT lowercase or trim

// Fetch correct answer
$q = $conn->query("SELECT answer FROM quiz WHERE id = $id");

if (!$q || $q->num_rows === 0) {
    echo "invalid||";
    exit;
}

$row = $q->fetch_assoc();
$correct = $row['answer']; // DO NOT modify

// STRICT CASE-SENSITIVE MATCH
if ($user === $correct) {
    echo "correct||" . htmlspecialchars($correct, ENT_QUOTES, "UTF-8");
} else {
    echo "wrong||" . htmlspecialchars($correct, ENT_QUOTES, "UTF-8");
}

$conn->close();
