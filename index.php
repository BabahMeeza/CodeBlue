<?php
require_once 'config/db.php';

// Detect IP
$ip_address = $_SERVER['REMOTE_ADDR'];

// Search room
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE ip_address = :ip");
$stmt->execute(['ip' => $ip_address]);
$room = $stmt->fetch();

// Redirect khusus untuk IGD / ICU agar langsung masuk ke mode Tim Code Blue
if ($room && (stripos($room['room_name'], 'IGD') !== false || stripos($room['room_name'], 'ICU') !== false)) {
    header("Location: tim.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petugas Dashboard - Code Blue System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="brand">
            <h1>Sistem <span>Code Blue</span></h1>
        </div>
        <div class="nav">
            <a href="tim.php" class="btn btn-blue" style="margin-right:10px;">Dashboard Tim</a>
            <a href="admin.php" class="btn btn-blue">Admin Panel</a>
        </div>
    </header>

    <div class="container call-container">
        <?php if ($room): ?>
            <div class="room-info">
                Anda berada di: <strong><?= htmlspecialchars($room['room_name']) ?></strong> (Lantai <?= htmlspecialchars($room['floor']) ?>)<br>
                IP Address: <?= htmlspecialchars($ip_address) ?>
            </div>
            
            <button class="btn-code-blue" onclick="callCodeBlue('<?= htmlspecialchars($ip_address) ?>')">
                PANGGIL<br>CODE BLUE
            </button>
        <?php else: ?>
            <div class="card" style="max-width: 600px; margin: 0 auto; text-align: center;">
                <h2 style="color: var(--primary-red); margin-bottom: 20px;">Akses Ditolak</h2>
                <p style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 20px;">
                    IP Address Anda (<strong><?= htmlspecialchars($ip_address) ?></strong>) belum terdaftar dalam sistem.
                </p>
                <p>Silakan hubungi Super Admin untuk mendaftarkan komputer ini ke suatu ruangan.</p>
                <br>
                <a href="admin.php" class="btn btn-blue">Masuk ke Admin Panel</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
