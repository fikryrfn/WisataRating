<?php
require __DIR__ . '/Server/auth.php';  // sesuaikan path relatifnya
auth_session();
include 'Server/koneksi.php';

// Hanya role 'admin_akun' yang boleh akses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin_akun') {
    header("Location: login.php");
    exit();
}

$dest_names = [
    1 => 'Pantai Kuta', 2 => 'Gunung Bromo', 3 => 'Candi Borobudur',
    4 => 'Air Terjun Tumpak Sewu', 5 => 'Pantai Raja Ampat',
    6 => 'Gunung Rinjani', 7 => 'Candi Prambanan',
    8 => 'Air Terjun Gitgit', 9 => 'Pantai Pink Lombok'
];

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'users';  // 'users' atau 'ulasan'

// Data edit user
$edit_user = null;
if (isset($_GET['edit_user'])) {
    $eu_id = (int)$_GET['edit_user'];
    $res   = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$eu_id'");
    $edit_user = mysqli_fetch_assoc($res);
}

// Data edit ulasan
$edit_ulasan = null;
if (isset($_GET['edit_ulasan'])) {
    $el_id   = (int)$_GET['edit_ulasan'];
    $res     = mysqli_query($koneksi, "SELECT * FROM ulasan WHERE id='$el_id'");
    $edit_ulasan = mysqli_fetch_assoc($res);
}

// Ambil semua users
$users_query = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id ASC");

// Ambil semua ulasan + join username
$ulasan_query = mysqli_query($koneksi,
    "SELECT u.*, usr.username 
     FROM ulasan u 
     LEFT JOIN users usr ON u.user_id = usr.id 
     ORDER BY u.tanggal DESC"
);

// Stat
$total_users  = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM users"));
$total_ulasan = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM ulasan"));
$total_admin  = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM users WHERE role != 'user'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Akun – Manajemen Pengguna | WisataRating</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6f9;
      display: flex;
      min-height: 100vh;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      width: 250px;
      background: #2c3e50;
      color: white;
      display: flex;
      flex-direction: column;
      position: fixed;
      height: 100vh;
      z-index: 100;
    }
    .sidebar-header {
      padding: 22px 20px;
      background: rgba(0,0,0,0.3);
      font-size: 18px;
      font-weight: 700;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar-header span { font-size: 11px; font-weight: 400; display: block; margin-top: 3px; color: #abebc6; }
    .sidebar-menu { list-style: none; padding: 15px 0; flex: 1; }
    .sidebar-menu li a {
      display: flex; align-items: center; gap: 10px;
      padding: 13px 22px;
      color: #bdc3c7;
      text-decoration: none;
      font-size: 14px;
      transition: all 0.2s;
      border-left: 3px solid transparent;
    }
    .sidebar-menu li a:hover,
    .sidebar-menu li a.active {
      background: rgba(255,255,255,0.1);
      color: white;
      border-left: 3px solid #2ecc71;
    }
    .sidebar-menu li a .icon { font-size: 16px; }
    .sidebar-footer {
      padding: 16px 20px;
      border-top: 1px solid rgba(255,255,255,0.1);
      font-size: 12px;
      color: #95a5a6;
    }

    /* ===== MAIN ===== */
    .main-content {
      margin-left: 250px;
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .topbar {
      background: white;
      padding: 15px 28px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .topbar h2 { font-size: 16px; color: #2c3e50; }
    .topbar-right { display: flex; align-items: center; gap: 14px; }
    .user-badge {
      background: #eafaf1;
      color: #1e8449;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
    }
    .btn-logout {
      background: #e74c3c;
      color: white;
      padding: 7px 16px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 13px;
      font-weight: 600;
    }
    .btn-logout:hover { background: #c0392b; }
    .content { padding: 28px; }

    /* ===== ALERT ===== */
    .alert {
      padding: 12px 18px;
      border-radius: 8px;
      margin-bottom: 18px;
      font-size: 14px;
    }
    .alert-success { background: #d5f5e3; color: #1e8449; border: 1px solid #a9dfbf; }
    .alert-info    { background: #d6eaf8; color: #1a5276; border: 1px solid #aed6f1; }
    .alert-warning { background: #fef9e7; color: #9a7d0a; border: 1px solid #f9e79f; }

    /* ===== STATS ===== */
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 24px; }
    .stat-card {
      background: white;
      border-radius: 10px;
      padding: 20px 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      display: flex; align-items: center; gap: 16px;
    }
    .stat-icon { font-size: 28px; }
    .stat-info p { font-size: 12px; color: #888; margin-bottom: 2px; }
    .stat-info h4 { font-size: 22px; font-weight: 700; color: #2c3e50; }

    /* ===== TABS ===== */
    .tabs { display: flex; gap: 0; margin-bottom: 20px; }
    .tab-btn {
      padding: 11px 22px;
      background: white;
      border: 1px solid #dce1e8;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      color: #666;
      transition: 0.2s;
    }
    .tab-btn:first-child { border-radius: 8px 0 0 8px; }
    .tab-btn:last-child  { border-radius: 0 8px 8px 0; border-left: none; }
    .tab-btn.active      { background: #2c3e50; color: white; border-color: #2c3e50; }

    /* ===== EDIT PANEL ===== */
    .edit-panel {
      background: white;
      border-radius: 10px;
      padding: 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.07);
      margin-bottom: 22px;
      border-left: 4px solid #27ae60;
    }
    .edit-panel h3 { color: #1e8449; margin-bottom: 18px; font-size: 16px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px; }
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 14px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 13px; font-weight: 600; color: #555; }
    .form-group input,
    .form-group textarea,
    .form-group select {
      border: 1px solid #dce1e8;
      border-radius: 7px;
      padding: 9px 12px;
      font-size: 14px;
      font-family: inherit;
      transition: 0.2s;
      outline: none;
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
      border-color: #27ae60;
      box-shadow: 0 0 0 3px rgba(39,174,96,0.1);
    }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .form-group small { font-size: 11px; color: #999; }
    .password-display {
      font-family: monospace;
      font-size: 11px;
      color: #888;
      background: #f8f9fa;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #eee;
      word-break: break-all;
    }
    .btn-row { display: flex; gap: 10px; margin-top: 6px; }
    .btn-save {
      background: #27ae60;
      color: white;
      border: none;
      padding: 9px 22px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
    }
    .btn-save:hover { background: #1e8449; }
    .btn-cancel {
      background: #ecf0f1;
      color: #555;
      padding: 9px 22px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
    }
    .btn-cancel:hover { background: #dde1e3; }

    /* ===== CARD TABLE ===== */
    .card {
      background: white;
      border-radius: 10px;
      padding: 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.07);
    }
    .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
    .card-header h3 { font-size: 16px; color: #2c3e50; }
    table { width: 100%; border-collapse: collapse; }
    th, td {
      padding: 11px 14px;
      text-align: left;
      border-bottom: 1px solid #eee;
      font-size: 13px;
    }
    th {
      background: #f8f9fa;
      font-weight: 700;
      color: #555;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }
    tr:hover td { background: #fafbff; }
    .badge-role {
      padding: 3px 10px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .role-user      { background: #eaf4fc; color: #2980b9; }
    .role-admin     { background: #fef9e7; color: #d4ac0d; }
    .role-admin_akun { background: #f9ebea; color: #c0392b; }
    .badge-rating {
      background: #fef9e7;
      color: #d4ac0d;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 12px;
      font-size: 12px;
    }
    .badge-dest {
      background: #eafaf1;
      color: #1e8449;
      padding: 3px 10px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 600;
    }
    .pass-cell {
      font-family: monospace;
      font-size: 10px;
      color: #aaa;
      max-width: 140px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .isi-cell {
      max-width: 200px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .btn-edit   { color: #27ae60; text-decoration: none; font-weight: 600; font-size: 12px; margin-right: 8px; }
    .btn-delete { color: #e74c3c; text-decoration: none; font-weight: 600; font-size: 12px; }
    .btn-edit:hover, .btn-delete:hover { text-decoration: underline; }
    .empty-row td { text-align: center; color: #aaa; padding: 40px; }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="sidebar-header">
    🗺️ WisataRating
    <span>Panel Admin – Manajemen Akun</span>
  </div>
  <ul class="sidebar-menu">
    <li>
      <a href="admin_akun.php?tab=users" class="<?= $tab === 'users' ? 'active' : '' ?>">
        <span class="icon">👥</span> Manajemen Pengguna
      </a>
    </li>
    <li>
      <a href="admin_akun.php?tab=ulasan" class="<?= $tab === 'ulasan' ? 'active' : '' ?>">
        <span class="icon">💬</span> Semua Ulasan
      </a>
    </li>
    <li><a href="home.php"><span class="icon">🏠</span> Lihat Website</a></li>
  </ul>
  <div class="sidebar-footer">
    Masuk sebagai: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong><br>
    Role: <strong>Admin Manajemen Akun</strong>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
  <div class="topbar">
    <h2>🔐 Manajemen Akun &amp; Ulasan</h2>
    <div class="topbar-right">
      <div class="user-badge">👤 <?= htmlspecialchars($_SESSION['username']) ?></div>
      <a href="Proses/logout.php" class="btn-logout">Keluar</a>
    </div>
  </div>

  <div class="content">

    <!-- Alert Pesan -->
    <?php
    $msg_map = [
      'user_updated'    => ['success', '✅ Data pengguna berhasil diperbarui!'],
      'user_deleted'    => ['info',    '🗑️ Pengguna berhasil dihapus beserta ulasannya.'],
      'ulasan_updated'  => ['success', '✅ Ulasan berhasil diperbarui!'],
      'ulasan_deleted'  => ['info',    '🗑️ Ulasan berhasil dihapus.'],
    ];
    if ($msg && isset($msg_map[$msg])):
      [$type, $text] = $msg_map[$msg];
    ?>
      <div class="alert alert-<?= $type ?>"><?= $text ?></div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info"><p>Total Pengguna</p><h4><?= $total_users ?></h4></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-info"><p>Total Ulasan</p><h4><?= $total_ulasan ?></h4></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🛡️</div>
        <div class="stat-info"><p>Total Admin</p><h4><?= $total_admin ?></h4></div>
      </div>
    </div>

    <!-- TABS -->
    <div class="tabs">
      <a href="admin_akun.php?tab=users"
         class="tab-btn <?= $tab === 'users' ? 'active' : '' ?>">👥 Manajemen Pengguna</a>
      <a href="admin_akun.php?tab=ulasan"
         class="tab-btn <?= $tab === 'ulasan' ? 'active' : '' ?>">💬 Semua Ulasan</a>
    </div>

    <!-- ===== TAB USERS ===== -->
    <?php if ($tab === 'users'): ?>

      <!-- Form Edit User -->
      <?php if ($edit_user): ?>
      <div class="edit-panel">
        <h3>✏️ Edit Akun Pengguna — <?= htmlspecialchars($edit_user['username']) ?></h3>
        <form action="Proses/prosesEditUser.php" method="POST">
          <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
          <div class="form-row-3">
            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" value="<?= htmlspecialchars($edit_user['username']) ?>" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" value="<?= htmlspecialchars($edit_user['email']) ?>" required>
            </div>
            <div class="form-group">
              <label>Role</label>
              <select name="role">
                <option value="user"       <?= $edit_user['role'] === 'user'       ? 'selected' : '' ?>>User</option>
                <option value="admin"      <?= $edit_user['role'] === 'admin'      ? 'selected' : '' ?>>Admin (Kelola Ulasan)</option>
                <option value="admin_akun" <?= $edit_user['role'] === 'admin_akun' ? 'selected' : '' ?>>Admin (Kelola Akun)</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Password Baru <small>(kosongkan jika tidak ingin ubah)</small></label>
              <input type="text" name="password" placeholder="Isi untuk reset password...">
            </div>
            <div class="form-group">
              <label>Password Saat Ini (hash)</label>
              <div class="password-display"><?= htmlspecialchars($edit_user['password']) ?></div>
            </div>
          </div>
          <div class="btn-row">
            <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
            <a href="admin_akun.php?tab=users" class="btn-cancel">✕ Batal</a>
          </div>
        </form>
      </div>
      <?php endif; ?>

      <!-- Tabel Pengguna -->
      <div class="card">
        <div class="card-header">
          <h3>Daftar Semua Pengguna</h3>
          <span style="font-size:12px;color:#888;"><?= $total_users ?> pengguna terdaftar</span>
        </div>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Email</th>
              <th>Role</th>
              <th>Password (Hash)</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            mysqli_data_seek($users_query, 0);
            if (mysqli_num_rows($users_query) === 0):
            ?>
              <tr class="empty-row"><td colspan="6">Belum ada pengguna.</td></tr>
            <?php else: ?>
              <?php while ($u = mysqli_fetch_assoc($users_query)): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                  <span class="badge-role role-<?= $u['role'] ?>">
                    <?= $u['role'] === 'admin_akun' ? 'Admin Akun' : ucfirst($u['role']) ?>
                  </span>
                </td>
                <td class="pass-cell" title="<?= htmlspecialchars($u['password']) ?>">
                  <?= htmlspecialchars($u['password']) ?>
                </td>
                <td>
                  <?php if ($u['id'] != $_SESSION['user_id']): ?>
                    <a href="admin_akun.php?tab=users&edit_user=<?= $u['id'] ?>" class="btn-edit">✏️ Edit</a>
                    <a href="Proses/prosesHapusUser.php?id=<?= $u['id'] ?>"
                       class="btn-delete"
                       onclick="return confirm('Hapus pengguna ini beserta semua ulasannya?')">🗑️ Hapus</a>
                  <?php else: ?>
                    <span style="font-size:11px;color:#aaa;">— Akun Sendiri</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    <!-- ===== TAB ULASAN ===== -->
    <?php elseif ($tab === 'ulasan'): ?>

      <!-- Form Edit Ulasan -->
      <?php if ($edit_ulasan): ?>
      <div class="edit-panel">
        <h3>✏️ Edit Ulasan — ID #<?= $edit_ulasan['id'] ?></h3>
        <form action="Proses/prosesEditUlasan.php" method="POST">
          <input type="hidden" name="id" value="<?= $edit_ulasan['id'] ?>">
          <input type="hidden" name="referer" value="admin_akun">
          <div class="form-row">
            <div class="form-group">
              <label>Nama Pengulas</label>
              <input type="text" name="nama" value="<?= htmlspecialchars($edit_ulasan['nama']) ?>" required>
            </div>
            <div class="form-group">
              <label>Rating (1–5)</label>
              <select name="rating" required>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <option value="<?= $i ?>" <?= $edit_ulasan['rating'] == $i ? 'selected' : '' ?>>
                    <?= str_repeat('★', $i) ?> (<?= $i ?>)
                  </option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
          <div class="form-group" style="margin-bottom:16px;">
            <label>Isi Ulasan</label>
            <textarea name="isi" required><?= htmlspecialchars($edit_ulasan['isi']) ?></textarea>
          </div>
          <div class="btn-row">
            <button type="submit" class="btn-save">💾 Simpan</button>
            <a href="admin_akun.php?tab=ulasan" class="btn-cancel">✕ Batal</a>
          </div>
        </form>
      </div>
      <?php endif; ?>

      <!-- Tabel Ulasan -->
      <div class="card">
        <div class="card-header">
          <h3>Semua Ulasan Pengguna</h3>
          <span style="font-size:12px;color:#888;"><?= $total_ulasan ?> ulasan</span>
        </div>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Pengguna</th>
              <th>Destinasi</th>
              <th>Nama Pengulas</th>
              <th>Rating</th>
              <th>Isi Ulasan</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($ulasan_query) === 0): ?>
              <tr class="empty-row"><td colspan="8">Belum ada ulasan.</td></tr>
            <?php else: ?>
              <?php while ($ul = mysqli_fetch_assoc($ulasan_query)): ?>
              <tr>
                <td><?= $ul['id'] ?></td>
                <td>
                  <?php if ($ul['username']): ?>
                    <strong><?= htmlspecialchars($ul['username']) ?></strong>
                  <?php else: ?>
                    <span style="color:#aaa;font-style:italic;">Tamu</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge-dest">
                    <?= htmlspecialchars($dest_names[$ul['dest_id']] ?? '#'.$ul['dest_id']) ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($ul['nama']) ?></td>
                <td><span class="badge-rating">★ <?= $ul['rating'] ?></span></td>
                <td class="isi-cell" title="<?= htmlspecialchars($ul['isi']) ?>">
                  <?= htmlspecialchars($ul['isi']) ?>
                </td>
                <td><?= date('d M Y', strtotime($ul['tanggal'])) ?></td>
                <td>
                  <a href="admin_akun.php?tab=ulasan&edit_ulasan=<?= $ul['id'] ?>" class="btn-edit">✏️ Edit</a>
                  <a href="Proses/prosesHapusUlasan.php?id=<?= $ul['id'] ?>&ref=admin_akun"
                     class="btn-delete"
                     onclick="return confirm('Hapus ulasan ini?')">🗑️ Hapus</a>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    <?php endif; ?>
  </div>
</div>

</body>
</html>
