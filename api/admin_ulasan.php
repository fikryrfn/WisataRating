<?php
session_start();
include 'Server/koneksi.php';

// Hanya role 'admin' yang boleh akses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Data destinasi (sama seperti di detail.php)
$dest_names = [
    1 => 'Pantai Kuta', 2 => 'Gunung Bromo', 3 => 'Candi Borobudur',
    4 => 'Air Terjun Tumpak Sewu', 5 => 'Pantai Raja Ampat',
    6 => 'Gunung Rinjani', 7 => 'Candi Prambanan',
    8 => 'Air Terjun Gitgit', 9 => 'Pantai Pink Lombok'
];

// Filter berdasarkan dest_id jika ada
$filter_dest = isset($_GET['dest_id']) ? (int)$_GET['dest_id'] : 0;
$where = $filter_dest ? "WHERE dest_id = $filter_dest" : "";
$ulasan_query = mysqli_query($koneksi, "SELECT * FROM ulasan $where ORDER BY tanggal DESC");

// Pesan sukses / hapus
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// Data edit jika ada ?edit=id
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM ulasan WHERE id='$edit_id'");
    $edit_data = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin – Kelola Ulasan | WisataRating</title>
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
      background: #1a5276;
      color: white;
      display: flex;
      flex-direction: column;
      position: fixed;
      height: 100vh;
      z-index: 100;
    }
    .sidebar-header {
      padding: 22px 20px;
      background: rgba(0,0,0,0.25);
      font-size: 18px;
      font-weight: 700;
      letter-spacing: 0.5px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar-header span { font-size: 11px; font-weight: 400; display: block; margin-top: 3px; color: #aed6f1; }
    .sidebar-menu { list-style: none; padding: 15px 0; flex: 1; }
    .sidebar-menu li a {
      display: flex; align-items: center; gap: 10px;
      padding: 13px 22px;
      color: #aed6f1;
      text-decoration: none;
      font-size: 14px;
      transition: all 0.2s;
      border-left: 3px solid transparent;
    }
    .sidebar-menu li a:hover,
    .sidebar-menu li a.active {
      background: rgba(255,255,255,0.1);
      color: white;
      border-left: 3px solid #5dade2;
    }
    .sidebar-menu li a .icon { font-size: 16px; }
    .sidebar-footer {
      padding: 16px 20px;
      border-top: 1px solid rgba(255,255,255,0.1);
      font-size: 12px;
      color: #85c1e9;
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
      background: #eaf4fc;
      color: #1a5276;
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
      transition: 0.2s;
    }
    .btn-logout:hover { background: #c0392b; }

    /* ===== CONTENT ===== */
    .content { padding: 28px; }
    .alert {
      padding: 12px 18px;
      border-radius: 8px;
      margin-bottom: 18px;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .alert-success { background: #d5f5e3; color: #1e8449; border: 1px solid #a9dfbf; }
    .alert-info    { background: #d6eaf8; color: #1a5276; border: 1px solid #aed6f1; }

    /* ===== EDIT PANEL ===== */
    .edit-panel {
      background: white;
      border-radius: 10px;
      padding: 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.07);
      margin-bottom: 24px;
      border-left: 4px solid #2980b9;
    }
    .edit-panel h3 { color: #1a5276; margin-bottom: 18px; font-size: 16px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px; }
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
      border-color: #2980b9;
      box-shadow: 0 0 0 3px rgba(41,128,185,0.1);
    }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .btn-row { display: flex; gap: 10px; margin-top: 6px; }
    .btn-save {
      background: #2980b9;
      color: white;
      border: none;
      padding: 9px 22px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.2s;
    }
    .btn-save:hover { background: #1a6fa0; }
    .btn-cancel {
      background: #ecf0f1;
      color: #555;
      padding: 9px 22px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
      transition: 0.2s;
    }
    .btn-cancel:hover { background: #dde1e3; }

    /* ===== CARD TABLE ===== */
    .card {
      background: white;
      border-radius: 10px;
      padding: 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.07);
    }
    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 18px;
    }
    .card-header h3 { font-size: 16px; color: #2c3e50; }
    .filter-form { display: flex; gap: 10px; align-items: center; }
    .filter-form select {
      border: 1px solid #dce1e8;
      border-radius: 7px;
      padding: 7px 12px;
      font-size: 13px;
      outline: none;
    }
    .btn-filter {
      background: #1a5276;
      color: white;
      border: none;
      padding: 7px 14px;
      border-radius: 7px;
      font-size: 13px;
      cursor: pointer;
    }
    .btn-clear {
      background: #95a5a6;
      color: white;
      padding: 7px 14px;
      border-radius: 7px;
      font-size: 13px;
      text-decoration: none;
    }

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
    .isi-cell {
      max-width: 240px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .btn-edit { color: #2980b9; text-decoration: none; font-weight: 600; font-size: 12px; margin-right: 10px; }
    .btn-delete { color: #e74c3c; text-decoration: none; font-weight: 600; font-size: 12px; }
    .btn-edit:hover { text-decoration: underline; }
    .btn-delete:hover { text-decoration: underline; }
    .empty-row td { text-align: center; color: #aaa; padding: 40px; }

    /* ===== STAT CARDS ===== */
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 24px; }
    .stat-card {
      background: white;
      border-radius: 10px;
      padding: 20px 24px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .stat-icon { font-size: 28px; }
    .stat-info p { font-size: 12px; color: #888; margin-bottom: 2px; }
    .stat-info h4 { font-size: 22px; font-weight: 700; color: #2c3e50; }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="sidebar-header">
    🗺️ WisataRating
    <span>Panel Admin – Ulasan</span>
  </div>
  <ul class="sidebar-menu">
    <li><a href="admin_ulasan.php" class="active"><span class="icon">⭐</span> Kelola Ulasan</a></li>
    <li><a href="index.php"><span class="icon">🏠</span> Lihat Website</a></li>
  </ul>
  <div class="sidebar-footer">
    Masuk sebagai: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong><br>
    Role: <strong>Admin Ulasan</strong>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
  <div class="topbar">
    <h2>📋 Manajemen Ulasan Wisatawan</h2>
    <div class="topbar-right">
      <div class="user-badge">👤 <?= htmlspecialchars($_SESSION['username']) ?></div>
      <a href="Proses/logout.php" class="btn-logout">Keluar</a>
    </div>
  </div>

  <div class="content">

    <!-- Alert -->
    <?php if ($msg === 'updated'): ?>
      <div class="alert alert-success">✅ Ulasan berhasil diperbarui!</div>
    <?php elseif ($msg === 'deleted'): ?>
      <div class="alert alert-info">🗑️ Ulasan berhasil dihapus.</div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <?php
      $total_ulasan = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM ulasan"));
      $total_dest   = count($dest_names);
      $avg_q = mysqli_query($koneksi, "SELECT AVG(rating) as avg FROM ulasan");
      $avg_r = mysqli_fetch_assoc($avg_q);
      $avg_rating = $avg_r['avg'] ? number_format($avg_r['avg'], 1) : '0';
    ?>
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-info">
          <p>Total Ulasan</p>
          <h4><?= $total_ulasan ?></h4>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🏝️</div>
        <div class="stat-info">
          <p>Destinasi Terdaftar</p>
          <h4><?= $total_dest ?></h4>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-info">
          <p>Rata-rata Rating</p>
          <h4><?= $avg_rating ?></h4>
        </div>
      </div>
    </div>

    <!-- Form Edit Ulasan -->
    <?php if ($edit_data): ?>
    <div class="edit-panel">
      <h3>✏️ Edit Ulasan — ID #<?= $edit_data['id'] ?></h3>
      <form action="Proses/prosesEditUlasan.php" method="POST">
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <input type="hidden" name="referer" value="admin_ulasan">
        <div class="form-row">
          <div class="form-group">
            <label>Nama Pengulas</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($edit_data['nama']) ?>" required>
          </div>
          <div class="form-group">
            <label>Rating (1–5)</label>
            <select name="rating" required>
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>" <?= $edit_data['rating'] == $i ? 'selected' : '' ?>>
                  <?= str_repeat('★', $i) ?> (<?= $i ?>)
                </option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
          <label>Isi Ulasan</label>
          <textarea name="isi" required><?= htmlspecialchars($edit_data['isi']) ?></textarea>
        </div>
        <div class="btn-row">
          <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
          <a href="admin_ulasan.php" class="btn-cancel">✕ Batal</a>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <!-- Tabel Ulasan -->
    <div class="card">
      <div class="card-header">
        <h3>Daftar Semua Ulasan</h3>
        <form class="filter-form" method="GET">
          <select name="dest_id">
            <option value="">Semua Destinasi</option>
            <?php foreach ($dest_names as $did => $dname): ?>
              <option value="<?= $did ?>" <?= $filter_dest == $did ? 'selected' : '' ?>>
                <?= htmlspecialchars($dname) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn-filter">Filter</button>
          <?php if ($filter_dest): ?>
            <a href="admin_ulasan.php" class="btn-clear">Reset</a>
          <?php endif; ?>
        </form>
      </div>

      <table>
        <thead>
          <tr>
            <th>#</th>
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
            <tr class="empty-row"><td colspan="7">Belum ada ulasan di sini.</td></tr>
          <?php else: ?>
            <?php while ($row = mysqli_fetch_assoc($ulasan_query)): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td>
                <span class="badge-dest">
                  <?= htmlspecialchars($dest_names[$row['dest_id']] ?? 'Destinasi #'.$row['dest_id']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td><span class="badge-rating">★ <?= $row['rating'] ?></span></td>
              <td class="isi-cell" title="<?= htmlspecialchars($row['isi']) ?>">
                <?= htmlspecialchars($row['isi']) ?>
              </td>
              <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
              <td>
                <a href="admin_ulasan.php?edit=<?= $row['id'] ?>" class="btn-edit">✏️ Edit</a>
                <a href="Proses/prosesHapusUlasan.php?id=<?= $row['id'] ?>&ref=admin_ulasan"
                   class="btn-delete"
                   onclick="return confirm('Yakin ingin hapus ulasan ini?')">🗑️ Hapus</a>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
