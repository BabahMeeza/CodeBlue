<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In real life, use $_SERVER['REMOTE_ADDR']. For local testing, we might need to simulate or pass it.
    // XAMPP usually returns ::1 for localhost. We'll use HTTP_X_FORWARDED_FOR if available.
    $ip_address = $_SERVER['REMOTE_ADDR'];
    if (isset($_POST['simulate_ip'])) {
        $ip_address = $_POST['simulate_ip']; // For testing purposes only
    }

    // Find the room
    $stmt = $pdo->prepare("SELECT id FROM rooms WHERE ip_address = :ip");
    $stmt->execute(['ip' => $ip_address]);
    $room = $stmt->fetch();

    if ($room) {
        $room_id = $room['id'];
        
        // Check if there is already a pending call for this room
        $stmt_check = $pdo->prepare("SELECT id FROM code_blue_calls WHERE room_id = :room_id AND status = 'pending'");
        $stmt_check->execute(['room_id' => $room_id]);
        if ($stmt_check->fetch()) {
             echo json_encode(['status' => 'error', 'message' => 'Panggilan Code Blue dari ruangan ini sudah aktif dan menunggu respon!']);
             exit;
        }

        // Insert new call
        $stmt_insert = $pdo->prepare("INSERT INTO code_blue_calls (room_id, call_time, status) VALUES (:room_id, NOW(), 'pending')");
        if ($stmt_insert->execute(['room_id' => $room_id])) {
            echo json_encode(['status' => 'success', 'message' => 'Code Blue berhasil dipanggil!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memanggil Code Blue.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "IP Address Anda ($ip_address) tidak terdaftar sebagai ruangan! Harap hubungi Admin."]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
