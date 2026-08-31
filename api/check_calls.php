<?php
require_once '../config/db.php';
header('Content-Type: application/json');

// Get all pending calls with room info
$stmt = $pdo->prepare("
    SELECT c.id AS call_id, c.call_time, r.room_name, r.floor 
    FROM code_blue_calls c
    JOIN rooms r ON c.room_id = r.id
    WHERE c.status = 'pending'
    ORDER BY c.call_time ASC
");
$stmt->execute();
$calls = $stmt->fetchAll();

echo json_encode(['status' => 'success', 'data' => $calls]);
?>
