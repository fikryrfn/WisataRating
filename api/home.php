<?php
require __DIR__ . '/Server/auth.php';
auth_session();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Beranda – WisataRating</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .filter-btn.active { background-color: #15803d; color: #fff; border-color: #15803d; }
    .card-enter { animation: fadeUp .35s ease both; }
    @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }

    .sp-thead-top { background: linear-gradient(135deg, #065f46 0%, #0d7c57 100%); }
    .sp-thead-sub { background: #054f3a; }
    .sp-tr-even   { background: #fff; }
    .sp-tr-odd    { background: #f0fdf4; }
    .sp-tr-even:hover, .sp-tr-odd:hover { background: #dcfce7; transition: background .15s; }

    .bar-wrap { display:inline-flex; align-items:center; gap:6px; min-width:130px; }
    .bar-bg   { flex:1; background:#e5e7eb; border-radius:4px; height:7px; overflow:hidden; }
    .bar-fill { height:100%; border-radius:4px; background: linear-gradient(90deg,#10b981,#059669);
                transition: width .6s cubic-bezier(.4,0,.2,1); }

    /* Hero background */
    .hero-section {
      position: relative;
      overflow: hidden;
      background:
        repeating-linear-gradient(
          0deg, transparent, transparent 49px,
          rgba(255,255,255,0.03) 49px, rgba(255,255,255,0.03) 50px
        ),
        repeating-linear-gradient(
          90deg, transparent, transparent 49px,
          rgba(255,255,255,0.03) 49px, rgba(255,255,255,0.03) 50px
        ),
        radial-gradient(ellipse 70% 80% at 15% 50%, rgba(16,185,129,0.35) 0%, transparent 65%),
        radial-gradient(ellipse 60% 70% at 85% 30%, rgba(5,150,105,0.3) 0%, transparent 60%),
        linear-gradient(135deg, #064e3b 0%, #065f46 30%, #047857 60%, #065f46 100%);
    }

    .hero-dots {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(circle, rgba(255,255,255,0.15) 1.5px, transparent 1.5px);
      background-size: 36px 36px;
      background-position: 18px 18px;
    }

    .hero-circle {
      position: absolute;
      border-radius: 50%;
      border: 1.5px solid rgba(255,255,255,0.1);
    }

    .hero-wave {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      line-height: 0;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- NAVBAR -->
<nav class="bg-white shadow-sm sticky top-0 z-30">
  <div class="max-w-5xl mx-auto px-4 flex items-center justify-between h-14">
    <a href="home.php" class="flex items-center gap-2 text-lg font-bold text-green-700">
      <span class="text-2xl">🗺️</span> WisataRating
    </a>
    <div class="flex items-center gap-3">
      <?php if (isset($_SESSION['username'])): ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <a href="admin_ulasan.php" class="text-xs bg-blue-600 text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors">⭐ Panel Admin</a>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin_akun'): ?>
          <a href="admin_akun.php" class="text-xs bg-purple-600 text-white font-semibold px-3 py-1.5 rounded-lg hover:bg-purple-700 transition-colors">🛡️ Panel Admin Akun</a>
        <?php endif; ?>
        <span class="text-sm text-gray-600">👋 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="Proses/logout.php" class="text-sm text-red-500 hover:text-red-700 font-semibold">Keluar</a>
      <?php else: ?>
        <a href="login.php" class="text-sm text-gray-600 hover:text-green-700">Masuk</a>
        <a href="register.php" class="text-sm bg-green-700 text-white px-4 py-1.5 rounded-lg hover:bg-green-800 transition-colors">Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- HERO -->
<div class="hero-section text-white py-12 px-4 text-center">

  <!-- Titik-titik -->
  <div class="hero-dots"></div>

  <!-- Lingkaran dekorasi -->
  <div class="hero-circle" style="width:420px;height:420px;top:-180px;right:-100px;"></div>
  <div class="hero-circle" style="width:220px;height:220px;top:-60px;right:80px;"></div>
  <div class="hero-circle" style="width:300px;height:300px;bottom:-150px;left:-80px;"></div>
  <div class="hero-circle" style="width:140px;height:140px;bottom:10px;left:120px;"></div>
  <div class="hero-circle" style="width:100px;height:100px;top:20px;left:60px;"></div>

  <!-- Siluet pegunungan bawah -->
  <div class="hero-wave">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none" style="display:block;width:100%;height:50px;">
      <path fill="rgba(4,120,87,0.4)" d="M0,50 L160,15 L320,45 L480,8 L640,40 L800,12 L960,42 L1120,18 L1280,38 L1440,20 L1440,80 L0,80 Z"/>
      <path fill="rgba(6,95,70,0.35)" d="M0,65 L200,35 L400,58 L600,28 L800,55 L1000,30 L1200,52 L1440,35 L1440,80 L0,80 Z"/>
    </svg>
  </div>

  <!-- Konten -->
  <div class="relative z-10">
    <h1 class="text-2xl sm:text-3xl font-extrabold mb-2 tracking-tight" style="text-shadow:0 2px 12px rgba(0,0,0,0.2);">Temukan Wisata Favoritmu</h1>
    <p class="text-green-200 text-sm mb-6">Baca &amp; tulis ulasan destinasi wisata Indonesia</p>
    <div class="max-w-md mx-auto flex gap-2">
      <input id="searchInput" type="text" placeholder="Cari destinasi…"
        oninput="filterCards()"
        class="flex-1 px-4 py-2.5 rounded-xl text-sm text-gray-800 focus:outline-none shadow-md" />
      <button class="bg-white text-green-700 font-bold text-sm px-5 py-2.5 rounded-xl hover:bg-green-50 transition-colors shadow-md">Cari</button>
    </div>
  </div>
</div>

<!-- DESTINASI CARDS -->
<main class="max-w-5xl mx-auto px-4 py-8">
  <div class="flex gap-2 flex-wrap mb-6">
    <button onclick="setFilter(this,'Semua')"     class="filter-btn active text-xs font-semibold px-4 py-1.5 rounded-full border border-green-700 bg-green-700 text-white">Semua</button>
    <button onclick="setFilter(this,'Pantai')"    class="filter-btn text-xs font-semibold px-4 py-1.5 rounded-full border border-gray-300 text-gray-600 hover:border-green-600 hover:text-green-700">Pantai</button>
    <button onclick="setFilter(this,'Gunung')"    class="filter-btn text-xs font-semibold px-4 py-1.5 rounded-full border border-gray-300 text-gray-600 hover:border-green-600 hover:text-green-700">Gunung</button>
    <button onclick="setFilter(this,'Budaya')"    class="filter-btn text-xs font-semibold px-4 py-1.5 rounded-full border border-gray-300 text-gray-600 hover:border-green-600 hover:text-green-700">Budaya</button>
    <button onclick="setFilter(this,'Air Terjun')" class="filter-btn text-xs font-semibold px-4 py-1.5 rounded-full border border-gray-300 text-gray-600 hover:border-green-600 hover:text-green-700">Air Terjun</button>
  </div>
  <div id="cards-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5"></div>
  <p id="no-result" class="hidden text-center text-gray-500 py-16">Destinasi tidak ditemukan.</p>
</main>

<!-- ═══════════════════════════════════════════════════════
     TABEL BPS: Rata-Rata Pengeluaran Wisman (VAR 272)
════════════════════════════════════════════════════════ -->
<section class="max-w-5xl mx-auto px-4 pb-12">
  <div class="bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 py-4 border-b border-gray-200">
      <div>
        <h2 class="font-bold text-gray-800 text-base leading-snug" id="sp-judul">
          Rata-Rata Pengeluaran Wisatawan Mancanegara per Kunjungan Menurut Kebangsaan (US $)
        </h2>
        <p class="text-xs text-gray-400 mt-0.5">Sumber: Badan Pusat Statistik (BPS) Indonesia · Var 272</p>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        <label class="text-xs text-gray-500 font-medium">Tahun:</label>
        <select id="sp-th-select" onchange="loadSpendingData()"
          class="text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
          <option value="124">2024</option>
          <option value="123">2023</option>
          <option value="122">2022</option>
          <option value="121">2021</option>
          <option value="120">2020</option>
          <option value="119">2019</option>
        </select>
        <button onclick="loadSpendingData()"
          class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-1.5 rounded-lg transition-colors">
          Muat
        </button>
      </div>
    </div>

    <!-- Loading spinner -->
    <div id="sp-status" class="text-center py-12 text-sm text-gray-400">
      <svg class="animate-spin h-6 w-6 mx-auto mb-2 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
      </svg>
      Mengambil data dari BPS…
    </div>

    <!-- Tabel -->
    <div id="sp-table-wrap" class="hidden overflow-x-auto">
      <table class="w-full text-sm border-collapse">
        <thead>
          <tr class="sp-thead-top text-white">
            <th class="text-left px-6 py-3 font-semibold text-xs border-r border-emerald-900 w-2/5">
              Negara Tempat Tinggal
            </th>
            <th class="text-right px-6 py-3 font-semibold text-xs border-r border-emerald-900">
              Rata-Rata Pengeluaran per Kunjungan (US $)
            </th>
            <th class="text-center px-6 py-3 font-semibold text-xs w-44">
              Proporsi
            </th>
          </tr>
          <tr class="sp-thead-sub text-emerald-200 text-xs">
            <th class="border-r border-emerald-900 px-6 py-1.5 font-normal text-left"></th>
            <th id="sp-year-sub" class="border-r border-emerald-900 px-6 py-1.5 font-normal text-right">—</th>
            <th class="px-6 py-1.5 font-normal text-center">dari nilai tertinggi</th>
          </tr>
        </thead>
        <tbody id="sp-tbody"></tbody>
      </table>
    </div>

    <!-- Footer -->
    <div id="sp-footer" class="hidden px-6 py-3 border-t border-gray-100 flex items-center justify-between">
      <span id="sp-total-rows" class="text-xs text-gray-400"></span>
      <a href="https://www.bps.go.id" target="_blank"
         class="text-xs text-emerald-600 hover:underline font-medium">Lihat sumber BPS →</a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="text-center text-xs text-gray-400 py-6 border-t border-gray-200 bg-white">
  © 2025 WisataRating — Platform ulasan wisata Indonesia
</footer>

<script src="../scripts.js"></script>

<script>
const TH_MAP = {
  '114':'2014','115':'2015','116':'2016','117':'2017','118':'2018',
  '119':'2019','120':'2020','121':'2021','122':'2022','123':'2023',
  '124':'2024','125':'2025'
};

async function loadSpendingData() {
  const th     = document.getElementById('sp-th-select').value;
  const status = document.getElementById('sp-status');
  const wrap   = document.getElementById('sp-table-wrap');
  const footer = document.getElementById('sp-footer');
  const year   = TH_MAP[th] || th;

  wrap.classList.add('hidden');
  footer.classList.add('hidden');
  status.classList.remove('hidden');
  status.innerHTML = `
    <svg class="animate-spin h-6 w-6 mx-auto mb-2 text-emerald-500"
         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
    </svg>
    Mengambil data dari BPS…`;

  try {
    const res  = await fetch(`api_bps.php?var=272&th=${th}&domain=0000&_ts=${Date.now()}`);
    const json = await res.json();

    if (!json.success) throw new Error(json.message || 'Gagal memuat data BPS');

    renderTable(json.data, year);

  } catch (err) {
    status.innerHTML = `
      <div class="py-8 text-center">
        <p class="text-red-500 text-sm font-bold mb-1">⚠️ Gagal memuat data</p>
        <p class="text-gray-500 text-xs mb-3">${err.message}</p>
        <button onclick="loadSpendingData()"
          class="text-xs bg-emerald-600 text-white font-semibold px-4 py-1.5 rounded-lg hover:bg-emerald-700">
          Coba lagi
        </button>
      </div>`;
  }
}

function renderTable(d, yearLabel) {
  const status = document.getElementById('sp-status');
  const wrap   = document.getElementById('sp-table-wrap');
  const footer = document.getElementById('sp-footer');
  const tbody  = document.getElementById('sp-tbody');

  if (d.info?.judul) document.getElementById('sp-judul').textContent = d.info.judul;
  document.getElementById('sp-year-sub').textContent = yearLabel;

  const results = d.results || [];
  tbody.innerHTML = '';

  if (results.length === 0) {
    tbody.innerHTML = `
      <tr><td colspan="3" class="text-center py-12 text-gray-400 bg-gray-50">
        <div class="text-2xl mb-2">📭</div>
        <p class="text-sm font-medium">Data untuk tahun ${yearLabel} belum tersedia di BPS.</p>
        <p class="text-xs mt-1">Coba pilih tahun lain.</p>
      </td></tr>`;
  } else {
    const vals = results
      .filter(r => r.negara !== 'Rata- Rata' && r.negara !== 'Rata-Rata')
      .map(r => parseFloat(r.nilai?.[yearLabel] || 0))
      .filter(v => !isNaN(v) && v > 0);
    const maxVal = Math.max(...vals, 1);

    results.forEach((item, i) => {
      const isAvg = item.negara === 'Rata- Rata' || item.negara === 'Rata-Rata';
      const rawVal = item.nilai?.[yearLabel] ?? '';
      const num    = rawVal !== '' ? parseFloat(rawVal) : null;
      const dispVal = num !== null
        ? new Intl.NumberFormat('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2}).format(num)
        : '—';
      const pct = (!isAvg && num !== null) ? Math.round((num / maxVal) * 100) : 0;

      const tr = document.createElement('tr');
      tr.className = isAvg
        ? 'bg-emerald-50 border-b border-emerald-100 font-bold'
        : (i % 2 === 0 ? 'sp-tr-even border-b' : 'sp-tr-odd border-b');

      tr.innerHTML = `
        <td class="px-6 py-2.5 text-xs font-medium ${isAvg ? 'text-emerald-800' : 'text-gray-700'} border-r border-gray-100">
          ${isAvg ? '📊 ' : ''}${item.negara}
        </td>
        <td class="px-6 py-2.5 text-xs text-right font-mono border-r border-gray-100">
          ${num !== null
            ? `<span class="text-gray-400 mr-0.5 text-[10px]">US$</span><span class="${isAvg?'text-emerald-700 font-bold':'text-gray-800'}">${dispVal}</span>`
            : '<span class="text-gray-300">—</span>'}
        </td>
        <td class="px-6 py-2.5 text-xs">
          ${!isAvg && num !== null ? `
            <div class="bar-wrap">
              <div class="bar-bg"><div class="bar-fill" style="width:${pct}%"></div></div>
              <span class="text-gray-400 text-[10px] w-9 text-right tabular-nums">${pct}%</span>
            </div>` : ''}
        </td>`;
      tbody.appendChild(tr);
    });
  }

  status.classList.add('hidden');
  wrap.classList.remove('hidden');
  footer.classList.remove('hidden');

  const dataRows = results.filter(r => r.negara !== 'Rata- Rata').length;
  document.getElementById('sp-total-rows').textContent =
    `Menampilkan ${dataRows} negara/wilayah · Tahun ${yearLabel}`;
}

document.addEventListener('DOMContentLoaded', loadSpendingData);
</script>
</body>
</html>
