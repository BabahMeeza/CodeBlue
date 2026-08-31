<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List rooms
    $stmt = $pdo->query("SELECT * FROM rooms ORDER BY id DESC");
    $rooms = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'data' => $rooms]);
} elseif ($method === 'POST') {
    // Add room
    if (isset($_POST['ip_address']) && isset($_POST['room_name']) && isset($_POST['floor'])) {
        $ip = $_POST['ip_address'];
        $name = $_POST['room_name'];
        $floor = $_POST['floor'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO rooms (ip_address, room_name, floor) VALUES (?, ?, ?)");
            $stmt->execute([$ip, $name, $floor]);
            echo json_encode(['status' => 'success', 'message' => 'Room added']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add room, IP might be duplicate']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
    }
} elseif ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    if (isset($_DELETE['id'])) {
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->execute([$_DELETE['id']]);
        echo json_encode(['status' => 'success', 'message' => 'Room deleted']);
    }
}
?>
