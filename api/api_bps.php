<?php
/**
 * api_bps.php — Proxy BPS API untuk WisataRating
 * Mendukung: var=272, var=331
 *
 * Normal : api_bps.php?var=272&th=124&domain=0000
 * Debug  : api_bps.php?var=272&th=124&domain=0000&debug=1
 *
 * STRUKTUR RESPONSE BPS v1 (terverifikasi):
 *   $bps['vervar']      = [{val, label}]   ← baris/negara
 *   $bps['turvar']      = [{val, label}]   ← tipe var baris (val=0 "Tidak ada")
 *   $bps['tahun']       = [{val, label}]   ← tahun aktif
 *   $bps['turtahun']    = [{val, label}]   ← tipe tahun (val=0 "Tahun")
 *   $bps['datacontent'] = {key => nilai}   ← data angka
 *
 *   Rumus key: vervar_val + var + turvar_val + tahun_val + turtahun_val
 *   Contoh   : 1 + 272 + 0 + 124 + 0 = "127201240" = 1064.9 (Brunei)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('BPS_API_KEY',  'fb8eb71d858a6a6f982162982f3b9459');
define('BPS_BASE_URL', 'https://webapi.bps.go.id/v1/api/list');
define('CACHE_DIR',    __DIR__ . '/');
define('CACHE_TTL',    86400);

// ── Parameter ─────────────────────────────────────────────────────────────────
$var    = isset($_GET['var'])    ? (int)$_GET['var']                                   : 272;
$th     = isset($_GET['th'])     ? (int)$_GET['th']                                    : 124;
$domain = isset($_GET['domain']) ? preg_replace('/[^0-9a-zA-Z]/', '', $_GET['domain']) : '0000';
$debug  = isset($_GET['debug'])  && $_GET['debug'] === '1';

$thYearMap = [
    114=>'2014',115=>'2015',116=>'2016',117=>'2017',118=>'2018',
    119=>'2019',120=>'2020',121=>'2021',122=>'2022',123=>'2023',
    124=>'2024',125=>'2025',
];
$yearLabel = $thYearMap[$th] ?? (string)$th;

// ── Cache ─────────────────────────────────────────────────────────────────────
$cacheFile = CACHE_DIR . "bps_{$var}_{$domain}_{$th}.json";

if (!$debug && file_exists($cacheFile) && (time() - filemtime($cacheFile) < CACHE_TTL)) {
    $c = json_decode(file_get_contents($cacheFile), true);
    if (!empty($c['success'])) { echo json_encode($c, JSON_UNESCAPED_UNICODE); exit; }
}

// ── Fetch BPS API ─────────────────────────────────────────────────────────────
$url = BPS_BASE_URL . "/model/data/lang/ind/domain/{$domain}/var/{$var}/th/{$th}/key/" . BPS_API_KEY;

$ctx = stream_context_create([
    'http' => ['timeout'=>20, 'user_agent'=>'Mozilla/5.0 WisataRating/1.0'],
    'ssl'  => ['verify_peer'=>false, 'verify_peer_name'=>false],
]);
$raw = @file_get_contents($url, false, $ctx);

if ($raw === false) {
    if (file_exists($cacheFile)) {
        $c = json_decode(file_get_contents($cacheFile), true);
        if ($c) { echo json_encode($c, JSON_UNESCAPED_UNICODE); exit; }
    }
    echo json_encode(['success'=>false, 'message'=>'Gagal terhubung ke BPS API', 'url'=>$url]);
    exit;
}

$bps = json_decode($raw, true);

// ── Debug ─────────────────────────────────────────────────────────────────────
if ($debug) {
    echo json_encode([
        'url'          => $url,
        'status'       => $bps['status'] ?? null,
        'top_keys'     => array_keys($bps ?? []),
        'vervar_count' => count($bps['vervar'] ?? []),
        'dc_count'     => count($bps['datacontent'] ?? []),
        'dc_sample'    => array_slice($bps['datacontent'] ?? [], 0, 5, true),
        'raw'          => $bps,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if (!$bps || strtolower($bps['status'] ?? '') !== 'ok') {
    echo json_encode(['success'=>false, 'message'=>'Status BPS: '.($bps['status']??'null')]);
    exit;
}

// ══════════════════════════════════════════════════════════════════════════════
//  PARSE — field ada di level TERATAS $bps, bukan di $bps['data']
// ══════════════════════════════════════════════════════════════════════════════
$vervar      = $bps['vervar']      ?? [];   // [{val, label}] — daftar negara
$turvar      = $bps['turvar']      ?? [];   // [{val, label}] — biasanya val=0
$tahun       = $bps['tahun']       ?? [];   // [{val, label}] — tahun aktif
$turtahun    = $bps['turtahun']    ?? [];   // [{val, label}] — biasanya val=0
$datacontent = $bps['datacontent'] ?? [];

// Ambil val komponen untuk susun key
$turvarVal   = (string)($turvar[0]['val']   ?? '0');
$turtahunVal = (string)($turtahun[0]['val'] ?? '0');
$tahunVal    = (string)($tahun[0]['val']    ?? $th);

// Judul dari BPS
$judulBps = $bps['var'][0]['label']
         ?? ($bps['subject'][0]['label'] ?? null);
$unitBps  = $bps['unit'] ?? '';
$judul    = ($judulBps ? $judulBps . ($unitBps ? " ({$unitBps})" : '') : 'Data BPS')
          . ' — ' . $yearLabel;

// ── Susun rows ────────────────────────────────────────────────────────────────
$results = [];

foreach ($vervar as $item) {
    $vervarVal = (string)($item['val']   ?? '');
    $nama      =          $item['label'] ?? '?';

    // Rumus key: vervar_val + var + turvar_val + tahun_val + turtahun_val
    $key = $vervarVal . (string)$var . $turvarVal . $tahunVal . $turtahunVal;

    // Cari nilai — coba string key dan int key
    $nilai = $datacontent[$key]
          ?? $datacontent[(int)$key]
          ?? null;

    // Fallback: scan key yang dimulai dengan vervarVal+var
    if ($nilai === null) {
        $prefix = $vervarVal . (string)$var;
        foreach ($datacontent as $k => $v) {
            if (strpos((string)$k, $prefix) === 0) { $nilai = $v; break; }
        }
    }

    $results[] = [
        'negara' => $nama,
        'nilai'  => [$yearLabel => $nilai !== null ? (string)$nilai : ''],
    ];
}

// ── Simpan & kembalikan ───────────────────────────────────────────────────────
$out = [
    'success' => true,
    'data'    => [
        'info'    => ['judul'=>$judul, 'tahun'=>$yearLabel, 'var'=>$var, 'th'=>$th],
        'results' => $results,
    ],
];

if (!empty($results)) {
    @file_put_contents($cacheFile, json_encode($out, JSON_UNESCAPED_UNICODE));
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
