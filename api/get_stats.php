<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$stmt = $pdo->query("
    SELECT 
        c.id, r.room_name, r.floor, c.call_time, c.response_time, c.status,
        TIMESTAMPDIFF(SECOND, c.call_time, c.response_time) as response_seconds
    FROM code_blue_calls c
    JOIN rooms r ON c.room_id = r.id
    ORDER BY c.call_time DESC
");
$history = $stmt->fetchAll();

// Calculate average SLA for responded calls
$avgStmt = $pdo->query("
    SELECT AVG(TIMESTAMPDIFF(SECOND, call_time, response_time)) as avg_seconds
    FROM code_blue_calls
    WHERE status = 'responded'
");
$avgResult = $avgStmt->fetch();
$avg_seconds = $avgResult['avg_seconds'] ? round($avgResult['avg_seconds']) : 0;

echo json_encode([
    'status' => 'success', 
    'history' => $history,
    'avg_response_time_seconds' => $avg_seconds
]);
?>
