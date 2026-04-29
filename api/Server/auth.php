<?php
// ============================================================
//  auth.php  –  Simpan di: api/Server/auth.php
//  Pengganti session untuk Vercel (serverless, no filesystem)
// ============================================================

define('AUTH_SECRET', 'WisataRating_S3cr3t_K3y_G4nti_Ini!');
define('AUTH_COOKIE', 'wr_auth');

/**
 * Set cookie login setelah berhasil autentikasi.
 */
function auth_set(int $user_id, string $username, string $email, string $role): void {
    $payload = base64_encode(json_encode([
        'user_id'  => $user_id,
        'username' => $username,
        'email'    => $email,
        'role'     => $role,
    ]));
    $sig = hash_hmac('sha256', $payload, AUTH_SECRET);

    setcookie(AUTH_COOKIE, $payload . '.' . $sig, [
        'expires'  => time() + 86400 * 7,   // 7 hari
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    // Isi $_SESSION agar kode lama tetap kompatibel di request ini
    $_SESSION['user_id']  = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['email']    = $email;
    $_SESSION['role']     = $role;
}

/**
 * Baca cookie dan isi $_SESSION — panggil di awal setiap halaman.
 */
function auth_session(): void {
    if (!isset($_COOKIE[AUTH_COOKIE])) return;

    $parts = explode('.', $_COOKIE[AUTH_COOKIE], 2);
    if (count($parts) !== 2) return;

    [$payload, $sig] = $parts;

    // Verifikasi signature agar cookie tidak bisa dipalsukan
    if (!hash_equals(hash_hmac('sha256', $payload, AUTH_SECRET), $sig)) return;

    $data = json_decode(base64_decode($payload), true);
    if (!$data) return;

    $_SESSION['user_id']  = $data['user_id'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['email']    = $data['email'];
    $_SESSION['role']     = $data['role'];
}

/**
 * Hapus cookie saat logout.
 */
function auth_clear(): void {
    setcookie(AUTH_COOKIE, '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $_SESSION = [];
}
