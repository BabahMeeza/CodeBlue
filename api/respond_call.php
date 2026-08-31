<?php
require_once '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['call_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing call_id']);
        exit;
    }
    
    $call_id = $_POST['call_id'];

    $stmt = $pdo->prepare("UPDATE code_blue_calls SET status = 'responded', response_time = NOW() WHERE id = :call_id AND status = 'pending'");
    if ($stmt->execute(['call_id' => $call_id])) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Call responded successfully']);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Call not found or already responded']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update call']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
