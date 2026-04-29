<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Masuk – WisataRating</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md">

    <!-- Logo -->
    <div class="text-center mb-6">
      <a href="index.php" class="text-2xl font-bold text-green-700">🗺️ WisataRating</a>
      <p class="text-gray-500 text-sm mt-1">Masuk ke akun kamu</p>
    </div>

    <!-- Alert -->
    <?php if (isset($_GET['error'])): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4">
        <?php
          if ($_GET['error'] === 'password') echo '❌ Password salah. Silakan coba lagi.';
          elseif ($_GET['error'] === 'username') echo '❌ Username tidak ditemukan.';
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'register'): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">
        ✅ Registrasi berhasil! Silakan masuk.
      </div>
    <?php endif; ?>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
      <form action="Proses/prosesLogin.php" method="POST">

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <input type="text" name="username" placeholder="Masukkan username" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" name="password" placeholder="Masukkan password" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" />
        </div>

        <div class="flex items-center gap-2 mb-5">
          <input type="checkbox" name="remember" id="remember" class="accent-green-700" />
          <label for="remember" class="text-sm text-gray-600">Ingat Saya</label>
        </div>

        <button type="submit"
          class="w-full bg-green-700 text-white font-semibold py-2.5 rounded-lg hover:bg-green-800 transition-colors text-sm">
          Masuk
        </button>
      </form>

      <p class="text-center text-sm text-gray-500 mt-5">
        Belum punya akun?
        <a href="register.php" class="text-green-700 font-semibold hover:underline">Daftar sekarang</a>
      </p>
    </div>

  </div>
</body>
</html>
