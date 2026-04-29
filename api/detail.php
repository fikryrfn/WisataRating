<?php
require __DIR__ . '/Server/auth.php';  // sesuaikan path relatifnya
auth_session();
include 'Server/koneksi.php';

// Data destinasi (bisa dipindah ke database nanti)
$destinations = [
  1 => [
    "name" => "Pantai Kuta", "location" => "Bali", "category" => "Pantai",
    "rating" => 4.5, "reviews" => 2847,
    "image" => "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=900&q=80",
    "description" => "Pantai ikonik dengan ombak indah, sunset memukau, dan suasana yang ramai.",
    "jam_buka" => "24 Jam", "tiket" => "Gratis", "panjang" => "3 km", "waktu_terbaik" => "Apr – Sep"
  ],
  2 => [
    "name" => "Gunung Bromo", "location" => "Jawa Timur", "category" => "Gunung",
    "rating" => 4.8, "reviews" => 3120,
    "image" => "https://images.unsplash.com/photo-1589308078059-be1415eab4c3?w=900&q=80",
    "description" => "Kawah vulkanik aktif dengan lautan pasir yang menakjubkan dan pemandangan matahari terbit.",
    "jam_buka" => "24 Jam", "tiket" => "Rp 35.000", "ketinggian" => "2.329 mdpl", "waktu_terbaik" => "Apr – Okt"
  ],
  3 => [
    "name" => "Candi Borobudur", "location" => "Jawa Tengah", "category" => "Budaya",
    "rating" => 4.9, "reviews" => 4210,
    "image" => "https://images.unsplash.com/photo-1596402184320-417e7178b2cd?w=900&q=80",
    "description" => "Candi Buddha terbesar di dunia, warisan budaya UNESCO yang menakjubkan.",
    "jam_buka" => "06.00 – 17.00", "tiket" => "Rp 50.000", "dibangun" => "Abad ke-9", "waktu_terbaik" => "Mei – Okt"
  ],
  4 => [
    "name" => "Air Terjun Tumpak Sewu", "location" => "Jawa Timur", "category" => "Air Terjun",
    "rating" => 4.7, "reviews" => 1985,
    "image" => "https://images.unsplash.com/photo-1695323803671-b4ebee2c7484?w=900&q=80",
    "description" => "Air terjun spektakuler berbentuk tirai raksasa di lereng Gunung Semeru.",
    "jam_buka" => "07.00 – 16.00", "tiket" => "Rp 20.000", "ketinggian" => "120 m", "waktu_terbaik" => "Des – Mar"
  ],
  5 => [
    "name" => "Pantai Raja Ampat", "location" => "Papua Barat", "category" => "Pantai",
    "rating" => 4.9, "reviews" => 1560,
    "image" => "https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=900&q=80",
    "description" => "Surga bahari dengan keanekaragaman hayati laut terkaya di dunia.",
    "jam_buka" => "24 Jam", "tiket" => "Rp 500.000", "luas" => "40.000 km²", "waktu_terbaik" => "Okt – Apr"
  ],
  6 => [
    "name" => "Gunung Rinjani", "location" => "Lombok, NTB", "category" => "Gunung",
    "rating" => 4.7, "reviews" => 2340,
    "image" => "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=80",
    "description" => "Gunung berapi aktif kedua tertinggi di Indonesia dengan danau kawah Segara Anak.",
    "jam_buka" => "24 Jam", "tiket" => "Rp 150.000", "ketinggian" => "3.726 mdpl", "waktu_terbaik" => "Apr – Agt"
  ],
  7 => [
    "name" => "Candi Prambanan", "location" => "Yogyakarta", "category" => "Budaya",
    "rating" => 4.7, "reviews" => 3540,
    "image" => "https://images.unsplash.com/photo-1555400038-63f0ba517a47?w=900&q=80",
    "description" => "Kompleks candi Hindu terbesar di Indonesia, dibangun pada abad ke-9.",
    "jam_buka" => "06.00 – 17.00", "tiket" => "Rp 50.000", "dibangun" => "Abad ke-9", "waktu_terbaik" => "Mei – Okt"
  ],
  8 => [
    "name" => "Air Terjun Gitgit", "location" => "Bali", "category" => "Air Terjun",
    "rating" => 4.4, "reviews" => 1230,
    "image" => "https://images.unsplash.com/photo-1433086966358-54859d0ed716?w=900&q=80",
    "description" => "Air terjun setinggi 35 meter dikelilingi hutan tropis yang asri dan segar.",
    "jam_buka" => "08.00 – 17.00", "tiket" => "Rp 15.000", "ketinggian" => "35 m", "waktu_terbaik" => "Apr – Sep"
  ],
  9 => [
    "name" => "Pantai Pink Lombok", "location" => "Lombok, NTB", "category" => "Pantai",
    "rating" => 4.6, "reviews" => 987,
    "image" => "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=900&q=80",
    "description" => "Pantai unik dengan pasir berwarna merah muda yang langka, salah satu dari tujuh di dunia.",
    "jam_buka" => "24 Jam", "tiket" => "Rp 10.000", "panjang" => "1 km", "waktu_terbaik" => "Apr – Sep"
  ],
];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$dest = $destinations[$id] ?? null;
if (!$dest) { header("Location: index.php"); exit(); }

// Simpan ulasan ke database
$pesan_sukses = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_ulasan'])) {
  $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
  $rating  = (int)$_POST['rating'];
  $isi     = mysqli_real_escape_string($koneksi, $_POST['isi_ulasan']);
  $dest_id = (int)$_POST['dest_id'];

  if ($nama && $rating > 0 && $isi) {
    $user_id_val = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'NULL';
    $q = "INSERT INTO ulasan (dest_id, user_id, nama, rating, isi, tanggal) VALUES ('$dest_id', $user_id_val, '$nama', '$rating', '$isi', NOW())";
    mysqli_query($koneksi, $q);
    $pesan_sukses = 'Ulasan berhasil dikirim!';
  }
}

// Ambil ulasan dari database
$ulasan_list = [];
$q_ulasan = mysqli_query($koneksi, "SELECT * FROM ulasan WHERE dest_id = $id ORDER BY tanggal DESC LIMIT 10");
while ($row = mysqli_fetch_assoc($q_ulasan)) {
  $ulasan_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($dest['name']) ?> – WisataRating</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Navbar -->
  <nav class="bg-white shadow">
    <div class="max-w-5xl mx-auto px-4 flex items-center justify-between h-14">
      <a href="index.php" class="text-lg font-bold text-green-700">🗺️ WisataRating</a>
      <div class="flex items-center gap-3">
        <a href="index.php" class="text-sm text-gray-500 hover:text-green-700">← Kembali</a>
        <?php if (isset($_SESSION['username'])): ?>
          <span class="text-sm text-gray-600">👋 <?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="Proses/logout.php" class="text-sm text-red-500 hover:text-red-700 font-semibold">Keluar</a>
        <?php else: ?>
          <a href="login.php" class="text-sm bg-green-700 text-white px-4 py-1.5 rounded-lg hover:bg-green-800">Masuk</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <main class="max-w-3xl mx-auto px-4 py-8">

    <!-- Foto & Info -->
    <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
      <img src="<?= $dest['image'] ?>" alt="<?= htmlspecialchars($dest['name']) ?>"
           class="w-full h-56 sm:h-72 object-cover" />
      <div class="p-5">
        <div class="flex items-start justify-between gap-3 flex-wrap">
          <div>
            <h1 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($dest['name']) ?></h1>
            <p class="text-gray-500 text-sm mt-0.5">📍 <?= htmlspecialchars($dest['location']) ?></p>
          </div>
          <div class="text-right">
            <div class="text-amber-500 font-bold text-lg">★ <?= $dest['rating'] ?></div>
            <p class="text-gray-400 text-xs"><?= number_format($dest['reviews'], 0, ',', '.') ?> ulasan</p>
          </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-4">
          <span class="text-xs bg-green-100 text-green-700 font-semibold px-3 py-1 rounded-full"><?= $dest['category'] ?></span>
          <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-3 py-1 rounded-full">Tiket: <?= $dest['tiket'] ?></span>
        </div>

        <p class="text-gray-600 text-sm leading-relaxed mt-4"><?= htmlspecialchars($dest['description']) ?></p>

        <!-- Info grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5 pt-5 border-t border-gray-100">
          <div class="text-center">
            <p class="text-xs text-gray-400">Jam Buka</p>
            <p class="text-sm font-semibold text-gray-700"><?= $dest['jam_buka'] ?></p>
          </div>
          <div class="text-center">
            <p class="text-xs text-gray-400">Tiket</p>
            <p class="text-sm font-semibold text-gray-700"><?= $dest['tiket'] ?></p>
          </div>
          <div class="text-center">
            <p class="text-xs text-gray-400">Info</p>
            <p class="text-sm font-semibold text-gray-700">
              <?= isset($dest['ketinggian']) ? $dest['ketinggian'] : (isset($dest['panjang']) ? $dest['panjang'] : (isset($dest['luas']) ? $dest['luas'] : (isset($dest['dibangun']) ? $dest['dibangun'] : '-'))) ?>
            </p>
          </div>
          <div class="text-center">
            <p class="text-xs text-gray-400">Waktu Terbaik</p>
            <p class="text-sm font-semibold text-gray-700"><?= $dest['waktu_terbaik'] ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Ulasan dari database -->
    <div class="bg-white rounded-xl shadow p-5 mb-6">
      <h2 class="font-bold text-gray-800 mb-4">Ulasan Wisatawan</h2>

      <?php if (empty($ulasan_list)): ?>
        <p class="text-gray-400 text-sm text-center py-4">Belum ada ulasan. Jadilah yang pertama! 🌟</p>
      <?php else: ?>
        <div class="space-y-4">
          <?php foreach ($ulasan_list as $ul): ?>
            <div class="border border-gray-100 rounded-xl p-4 relative">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-xs font-bold text-green-700">
                    <?= strtoupper(substr($ul['nama'], 0, 2)) ?>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($ul['nama']) ?></p>
                    <p class="text-xs text-gray-400"><?= date('d M Y', strtotime($ul['tanggal'])) ?></p>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <span class="text-amber-500 text-sm font-bold">
                    <?= str_repeat('★', $ul['rating']) ?><span class="text-gray-300"><?= str_repeat('★', 5 - $ul['rating']) ?></span>
                  </span>
                  <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'admin_akun'])): ?>
                    <div class="flex gap-2">
                      <a href="admin_ulasan.php?edit=<?= $ul['id'] ?>"
                         class="text-xs bg-blue-50 text-blue-600 font-semibold px-2.5 py-1 rounded-lg hover:bg-blue-100 transition-colors">
                        ✏️ Edit
                      </a>
                      <a href="Proses/prosesHapusUlasan.php?id=<?= $ul['id'] ?>&ref=admin_ulasan"
                         class="text-xs bg-red-50 text-red-600 font-semibold px-2.5 py-1 rounded-lg hover:bg-red-100 transition-colors"
                         onclick="return confirm('Hapus ulasan ini?')">
                        🗑️ Hapus
                      </a>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              <p class="text-gray-600 text-sm leading-relaxed"><?= htmlspecialchars($ul['isi']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Form Ulasan -->
    <div class="bg-white rounded-xl shadow p-5">
      <h2 class="font-bold text-gray-800 mb-4">Tulis Ulasan</h2>

      <?php if ($pesan_sukses): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">
          ✅ <?= $pesan_sukses ?>
        </div>
      <?php endif; ?>

      <?php if (!isset($_SESSION['username'])): ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm px-4 py-3 rounded-lg mb-4">
          ⚠️ Kamu harus <a href="login.php" class="font-semibold underline">masuk</a> terlebih dahulu untuk menulis ulasan.
        </div>
      <?php endif; ?>

      <form action="detail.php?id=<?= $id ?>" method="POST">
        <input type="hidden" name="dest_id" value="<?= $id ?>" />

        <div class="mb-4">
          <p class="text-sm font-medium text-gray-700 mb-2">Rating</p>
          <div class="flex gap-1" id="stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <span onclick="setRating(<?= $i ?>)" class="star text-3xl text-gray-300 cursor-pointer hover:text-amber-400 transition-colors">★</span>
            <?php endfor; ?>
          </div>
          <input type="hidden" name="rating" id="rating-input" value="0" />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
          <input type="text" name="nama"
            value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>"
            placeholder="Nama kamu" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Ulasan</label>
          <textarea name="isi_ulasan" rows="3" placeholder="Ceritakan pengalamanmu..." required
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 resize-none"></textarea>
        </div>

        <button type="submit" name="kirim_ulasan" <?= !isset($_SESSION['username']) ? 'disabled' : '' ?>
          class="bg-green-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg hover:bg-green-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
          Kirim Ulasan
        </button>
      </form>
    </div>

  </main>

  <footer class="text-center text-xs text-gray-400 py-6 border-t border-gray-200 bg-white mt-4">
    © 2025 WisataRating — Platform ulasan wisata Indonesia
  </footer>

  <script>
    let selectedRating = 0;
    function setRating(n) {
      selectedRating = n;
      document.getElementById('rating-input').value = n;
      document.querySelectorAll('.star').forEach((s, i) => {
        s.style.color = i < n ? '#f59e0b' : '#d1d5db';
      });
    }
  </script>
</body>
</html>
