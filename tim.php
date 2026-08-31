<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tim Code Blue Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body id="team-dashboard">
    <header>
        <div class="brand">
            <h1>Dashboard <span>Tim Code Blue</span></h1>
        </div>
        <div class="nav">
            <a href="index.php" class="btn btn-blue" style="margin-right:10px;">Petugas</a>
            <a href="admin.php" class="btn btn-blue">Admin Panel</a>
        </div>
    </header>

    <div class="container" style="text-align: center; padding-top: 100px;">
        
        <div id="standby-container">
            <div class="card" style="display: inline-block;">
                <h2 style="color: var(--success); font-size: 2rem;">STATUS: STANDBY</h2>
                <p style="color: var(--text-muted); margin-top: 10px;">Belum ada panggilan Code Blue.</p>
                <p style="margin-top: 20px; font-size: 0.9rem; color: #f1c40f;">(Klik area mana saja untuk mengaktifkan sistem suara alarm)</p>
            </div>
        </div>

        <div id="alert-container" class="alert-container">
            <div class="alert-box">
                <h2>!!! PANGGILAN CODE BLUE !!!</h2>
                <p>DI RUANGAN <strong id="alert-room" style="color: white; font-weight: 800;">...</strong></p>
                <p>LANTAI <strong id="alert-floor" style="color: white; font-weight: 800;">...</strong></p>
            </div>
            
            <button class="btn btn-blue" onclick="respondToCall()" style="font-size: 1.5rem; padding: 20px 40px; background-color: var(--success);">
                SAYA SUDAH MERESPON
            </button>
        </div>

    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
