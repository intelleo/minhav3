<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Minha</title>
  <link rel="stylesheet" href="<?= base_url('css/output.css') ?>">
  <link rel="stylesheet" href="<?= base_url('fonts/remixicon.css') ?>">
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");
  </style>
</head>
<?= $this->include('partials/button_spinner') ?>

<body class="bg-gray-100 container mx-auto flex items-center gap-3 justify-center flex-col h-screen">
  <img src="<?= base_url('img/icon-tr.webp') ?>" alt="Logo" width="60" class="max-md:block hidden">
  <h2 class="text-2xl hidden max-md:block font-semibold">Hello, welcome!</h2>
  <!-- Alert -->
  <?= $this->include('partials/alert') ?>
  <div class="form-container">
    <form action="<?= site_url('doRegister') ?>" method="post" class="desktop-view" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <h1>Register</h1>



      <div class="f-group">
        <i class="ri-id-card-fill"></i>
        <input type="text" name="namalengkap" placeholder="Nama Lengkap" value="<?= old('namalengkap') ?>" required>
      </div>

      <div class="f-group">
        <i class="ri-school-fill"></i>
        <select name="jurusan" required class="cursor-pointer bg-transparent outline-none w-full">
          <option value="" disabled <?= old('jurusan') ? '' : 'selected' ?>>Pilih Jurusan</option>
          <option value="Komputerisasi Akuntansi" <?= old('jurusan') == 'Komputerisasi Akuntansi' ? 'selected' : '' ?>>Komputerisasi Akuntansi</option>
          <option value="Manajemen Informatika" <?= old('jurusan') == 'Manajemen Informatika' ? 'selected' : '' ?>>Manajemen Informatika</option>
          <option value="Sistem Informasi" <?= old('jurusan') == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
          <option value="Teknik Informatika" <?= old('jurusan') == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
          <option value="Sistem Komputer" <?= old('jurusan') == 'Sistem Komputer' ? 'selected' : '' ?>>Sistem Komputer</option>
          <option value="Hukum" <?= old('jurusan') == 'Hukum' ? 'selected' : '' ?>>Hukum</option>
          <option value="Administrasi Publik" <?= old('jurusan') == 'Administrasi Publik' ? 'selected' : '' ?>>Administrasi Publik</option>
          <option value="Kewirausahaan" <?= old('jurusan') == 'Kewirausahaan' ? 'selected' : '' ?>>Kewirausahaan</option>
        </select>
      </div>

      <div class="f-group">
        <i class="ri-user-3-fill"></i>
        <input type="text" name="npm" placeholder="NPM" value="<?= old('npm') ?>" required>
      </div>

      <div class="f-group">
        <i class="ri-shield-keyhole-fill"></i>
        <input type="password" name="password" placeholder="Masukkan Password" required>
      </div>

      <!-- <div class="f-group">
        <i class="ri-image-fill"></i>
        <input type="file" name="foto_profil" accept="image/*" class="w-full px-3 py-2">
      </div>

      <div class="f-group">
        <textarea name="bio" placeholder="Tentang saya..." class="w-full p-3 border rounded h-20"><?= old('bio') ?></textarea>
      </div> -->

      <button type="submit" data-spinner="button">Register</button>
      <p class="text-md hidden max-md:inline">
        Already have an account? <a href="/login" class="text-blue-500">Login</a>
      </p>
    </form>

    <div class="login">
      <img src="<?= base_url('img/icon-tr.webp') ?>" alt="Icon" width="50">
      <h2>hello, welcome! </h2>
      <p>already have an account?</p>
      <a href="/login">Login</a>
    </div>
  </div>

  <footer class="text-center mt-2 w-[90%] mx-auto text-sm">
    &copy; <?= date('Y') ?> Minha. All rights reserved.
  </footer>

  <!-- Spinner Loader Script -->
</body>

</html>