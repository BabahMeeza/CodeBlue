<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .stats-card { background: var(--surface-dark); padding: 20px; border-radius: 12px; text-align: center; border: 1px solid var(--border-color); }
        .stats-card h3 { font-size: 1rem; color: var(--text-muted); margin-bottom: 10px; }
        .stats-card .value { font-size: 2.5rem; color: var(--primary-blue); font-weight: 700; }
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab { padding: 10px 20px; cursor: pointer; border-radius: 8px; background: rgba(255,255,255,0.05); }
        .tab.active { background: var(--primary-blue); color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <header>
        <div class="brand">
            <h1>Admin <span>Panel</span></h1>
        </div>
        <div class="nav">
            <a href="index.php" class="btn btn-blue" style="margin-right:10px;">Petugas</a>
            <a href="tim.php" class="btn btn-blue">Tim Code Blue</a>
        </div>
    </header>

    <div class="container">
        <div class="stats-card" style="margin-bottom: 30px;">
            <h3>Rata-rata Waktu Respon (SLA)</h3>
            <div class="value" id="avg-sla">...</div>
        </div>

        <div class="tabs">
            <div class="tab active" onclick="switchTab('rooms')">Manajemen Ruangan</div>
            <div class="tab" onclick="switchTab('history')">Riwayat Panggilan</div>
        </div>

        <!-- Rooms Tab -->
        <div id="rooms-tab" class="tab-content active">
            <div class="grid">
                <!-- Add Room Form -->
                <div class="card">
                    <h2 style="margin-bottom: 20px;">Tambah Ruangan</h2>
                    <form id="addRoomForm" onsubmit="addRoom(event)">
                        <div class="form-group">
                            <label>IP Address</label>
                            <input type="text" id="ip_address" class="form-control" required placeholder="Contoh: 192.168.1.10">
                        </div>
                        <div class="form-group">
                            <label>Nama Ruangan</label>
                            <input type="text" id="room_name" class="form-control" required placeholder="Contoh: IGD">
                        </div>
                        <div class="form-group">
                            <label>Lantai</label>
                            <input type="text" id="floor" class="form-control" required placeholder="Contoh: 1">
                        </div>
                        <button type="submit" class="btn btn-blue" style="width: 100%;">Simpan</button>
                    </form>
                </div>

                <!-- Room List -->
                <div class="card">
                    <h2 style="margin-bottom: 20px;">Daftar Ruangan</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Ruangan</th>
                                <th>Lantai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="rooms-list">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div id="history-tab" class="tab-content">
            <div class="card">
                <h2 style="margin-bottom: 20px;">Riwayat Code Blue</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Waktu Panggilan</th>
                            <th>Ruangan</th>
                            <th>Lantai</th>
                            <th>Waktu Respon</th>
                            <th>SLA (Detik)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="history-list">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabId + '-tab').classList.add('active');
            
            if (tabId === 'history') loadStats();
            if (tabId === 'rooms') loadRooms();
        }

        async function loadRooms() {
            const res = await fetch('api/manage_rooms.php');
            const data = await res.json();
            const tbody = document.getElementById('rooms-list');
            tbody.innerHTML = '';
            
            data.data.forEach(room => {
                tbody.innerHTML += `
                    <tr>
                        <td>${room.ip_address}</td>
                        <td>${room.room_name}</td>
                        <td>${room.floor}</td>
                        <td><button class="btn" style="background:#e74c3c; padding: 5px 10px; font-size: 0.8rem;" onclick="deleteRoom(${room.id})">Hapus</button></td>
                    </tr>
                `;
            });
        }

        async function addRoom(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('ip_address', document.getElementById('ip_address').value);
            formData.append('room_name', document.getElementById('room_name').value);
            formData.append('floor', document.getElementById('floor').value);

            const res = await fetch('api/manage_rooms.php', { method: 'POST', body: formData });
            const result = await res.json();
            
            if (result.status === 'success') {
                document.getElementById('addRoomForm').reset();
                loadRooms();
            } else {
                alert(result.message);
            }
        }

        async function deleteRoom(id) {
            if(!confirm("Yakin hapus?")) return;
            const res = await fetch('api/manage_rooms.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            });
            const result = await res.json();
            if(result.status === 'success') loadRooms();
        }

        async function loadStats() {
            const res = await fetch('api/get_stats.php');
            const data = await res.json();
            
            document.getElementById('avg-sla').innerText = data.avg_response_time_seconds + ' Detik';
            
            const tbody = document.getElementById('history-list');
            tbody.innerHTML = '';
            
            data.history.forEach(call => {
                const statusBadge = call.status === 'responded' ? '<span class="badge badge-responded">Selesai</span>' : '<span class="badge badge-pending">Pending</span>';
                tbody.innerHTML += `
                    <tr>
                        <td>${call.call_time}</td>
                        <td>${call.room_name}</td>
                        <td>${call.floor}</td>
                        <td>${call.response_time || '-'}</td>
                        <td>${call.response_seconds || '-'}</td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
            });
        }

        // Initialize
        loadRooms();
        loadStats();
    </script>
</body>
</html>
