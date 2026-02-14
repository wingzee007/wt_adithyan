<?php
session_start();
header("Content-Type: application/json");

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "reply" => "Method not allowed"
    ]);
    exit;
}

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/config.php";

// Ensure API key exists
if (!defined("OPENAI_API_KEY") || empty(OPENAI_API_KEY)) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "reply" => "Server configuration error"
    ]);
    exit;
}

/* ---------- INPUT ---------- */
$input = json_decode(file_get_contents("php://input"), true);
$userMessage = trim($input['message'] ?? '');

if ($userMessage === '') {
    echo json_encode([
        "success" => false,
        "reply" => "Please type a question.",
        "history" => []
    ]);
    exit;
}

$userId = $_SESSION['user_id'] ?? 1;

/* ---------- SAVE QUESTION ---------- */
$stmt = $conn->prepare(
    "INSERT INTO user_questions (user_id, question) VALUES (?, ?)"
);
$stmt->bind_param("is", $userId, $userMessage);
$stmt->execute();
$stmt->close();

/* ---------- OPENAI REQUEST (FORMATTED OUTPUT FEATURE) ---------- */
$payload = [
    "model" => "gpt-4o-mini",
    "input" => [
[
    "role" => "system",
    "content" =>
        "You are a programming tutor AI.

        OUTPUT FORMAT RULES (MANDATORY):

        - DO NOT write paragraphs.
        - DO NOT combine explanations into a single block.
        - Every explanation MUST be in points.
        - A BLANK LINE MUST EXIST AFTER EVERY NUMBERED POINT.

        STRUCTURE TO FOLLOW EXACTLY:

        1. Title (single line)

        (blank line)

        2. One-line definition

        (blank line)

        3. Numbered explanation points.
           - Format MUST be exactly like this:

             1. First point text.

             (blank line)

             2. Second point text.

             (blank line)

             3. Third point text.

           - Each point max 2 lines.
           - NEVER place two numbers directly next to each other.

        4. Example section
           - Label it exactly as 'Example:'
           - Leave a blank line before and after 'Example:'
           - Code MUST be inside triple backticks with the language name.
           - Proper indentation is required.

        5. Key Points section
           - Label it exactly as 'Key Points:'
           - Use bullet points.
           - Leave a blank line between bullets.

        IMPORTANT:
        - If any number appears immediately after another number, the format is WRONG.
        - Spacing is mandatory and more important than content."
],



        [
            "role" => "user",
            "content" => $userMessage
        ]
    ]
];

$ch = curl_init("https://api.openai.com/v1/responses");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . OPENAI_API_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false || $httpCode !== 200) {
    curl_close($ch);
    echo json_encode([
        "success" => false,
        "reply" => "AI service is temporarily unavailable."
    ]);
    exit;
}

curl_close($ch);
$res = json_decode($response, true);

/* ---------- SAFE RESPONSE EXTRACTION ---------- */
$reply = "The AI is currently unavailable. Try again later.";

if (
    isset($res['output'][0]['content'][0]['text']) &&
    is_string($res['output'][0]['content'][0]['text'])
) {
    $reply = $res['output'][0]['content'][0]['text'];
}

/* ---------- FETCH HISTORY ---------- */
$history = [];
$hStmt = $conn->prepare(
    "SELECT question
     FROM user_questions
     WHERE user_id = ?
     ORDER BY id DESC
     LIMIT 20"
);

$hStmt->bind_param("i", $userId);
$hStmt->execute();
$result = $hStmt->get_result();

while ($row = $result->fetch_assoc()) {
    $history[] = $row['question'];
}

$hStmt->close();
$conn->close();

function enforceReadableFormat($text) {

    // Preserve code blocks
    preg_match_all('/```[\s\S]*?```/', $text, $codeBlocks);
    $placeholders = [];

    foreach ($codeBlocks[0] as $i => $block) {
        $key = "__CODE_BLOCK_$i__";
        $placeholders[$key] = $block;
        $text = str_replace($block, $key, $text);
    }

    // Add blank line between numbered points
    $text = preg_replace('/(\n\d+\.\s.*?)(\n)(\d+\.\s)/', "$1\n\n$3", $text);

    // Ensure spacing before Example:
    $text = preg_replace('/\nExample:/', "\n\nExample:", $text);

    // Ensure spacing before Key Points:
    $text = preg_replace('/\nKey Points:/', "\n\nKey Points:", $text);

    // Restore code blocks
    foreach ($placeholders as $key => $block) {
        $text = str_replace($key, $block, $text);
    }

    return trim($text);
}

/* ---------- OUTPUT ---------- */
echo json_encode([
    "success" => true,
    "reply" => $reply,
    "history" => array_values(array_unique($history))
]);
