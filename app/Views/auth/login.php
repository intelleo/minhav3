<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Minha</title>
  <link rel="stylesheet" href="<?= base_url('css/output.css') ?>">
  <link rel="stylesheet" href="<?= base_url('fonts/remixicon.css') ?>">
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");
  </style>
  <?= $this->include('partials/button_spinner') ?>
</head>

<body class="bg-gray-100 container mx-auto flex items-center gap-3 justify-center flex-col h-screen">
  <img src="<?= base_url('img/icon-tr.webp') ?>" alt="Logo" width="60" class="max-md:block hidden">
  <h2 class="text-2xl hidden max-md:block font-semibold">Hello, welcome!</h2>
  <!-- 🔥 Ganti ini dengan alert reusable -->
  <?= $this->include('partials/alert') ?>
  <div class="form-container">
    <div class="register">
      <img src="<?= base_url('img/icon-tr.webp') ?>" alt="Icon" width="50">
      <h2>hello, welcome! </h2>
      <p>don't have an account?</p>
      <a href="<?= site_url('register') ?>">Register</a>
    </div>

    <!-- ✅ Form action ke method login -->
    <form action="<?= site_url('login') ?>" method="post" class="desktop-view">
      <?= csrf_field() ?>
      <h1>Login</h1>
      <!-- ✅ Tampilkan pesan error jika ada -->


      <div class="f-group">
        <i class="ri-user-3-fill"></i>
        <!-- ✅ Tambahkan name dan value -->
        <input type="text" name="npm" placeholder="Masukkan NPM" value="<?= old('npm') ?>" required>
      </div>
      <div class="f-group">
        <i class="ri-shield-keyhole-fill"></i>
        <input type="password" name="password" placeholder="Masukkan Password" required>
      </div>
      <button type="submit" data-spinner="button">Login</button>
      <p class="text-md hidden max-md:inline">
        Don't have an account?
        <a href="<?= site_url('register') ?>" class="text-blue-500">Register</a>
      </p>
    </form>
  </div>

  <footer class="text-center mt-2 w-[90%] mx-auto text-sm">
    &copy; <?= date('Y') ?> Minha. All rights reserved.
  </footer>
  <!-- alphine js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Spinner Loader Script -->
</body>

</html>