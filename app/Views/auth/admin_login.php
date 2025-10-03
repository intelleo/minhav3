<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <meta name="base-url" content="<?= rtrim(site_url('/'), '/') ?>">
  <title>Admin Login - Minha</title>
  <link rel="stylesheet" href="<?= base_url('css/output.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/custom.css') ?>">

  <style>
    @keyframes float {

      0%,
      100% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(-20px);
      }
    }

    @keyframes gradient {
      0% {
        background-position: 0% 50%;
      }

      50% {
        background-position: 100% 50%;
      }

      100% {
        background-position: 0% 50%;
      }
    }

    .animate-float {
      animation: float 6s ease-in-out infinite;
    }

    .gradient-bg {
      background: linear-gradient(-45deg, #667eea, #247de3, #1c68c5, #1c68c5);
      background-size: 400% 400%;
      animation: gradient 15s ease infinite;
    }

    .glass-effect {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);

    }

    .input-focus {
      transition: all 0.3s ease;
    }

    .input-focus:focus {
      transform: translateY(-1px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-hover {
      transition: all 0.3s ease;
    }

    .btn-hover:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .social-btn {
      transition: all 0.3s ease;
    }

    .social-btn:hover {
      transform: scale(1.05);
    }
  </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
  <!-- Background Decorations -->
  <div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float"></div>
    <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float" style="animation-delay: 2s;"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-float" style="animation-delay: 4s;"></div>
  </div>

  <!-- Login Container -->
  <div class="relative z-10 w-full max-w-md">
    <!-- Logo/Brand -->
    <div class="text-center mb-4">
      <div class="flex items-center justify-center mb-2">
        <img src="<?= base_url('img/icon-tr.webp') ?>" alt="Logo" width="50" class="">
      </div>
      <h3 class=" text-2xl text-white font-bold">Login-Admin</h3>
    </div>

    <!-- Login Form (server POST, tanpa SPA) -->
    <div class="bg-white rounded-2xl shadow-2xl p-8">
      <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 p-3 bg-red-50 text-red-700 rounded border border-red-200">
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-4 p-3 bg-green-50 text-green-700 rounded border border-green-200">
          <?= esc(session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= site_url('admin/login') ?>" class="space-y-6" autocomplete="off">
        <?= csrf_field() ?>
        <!-- Email Input -->
        <div>
          <label for="username" class="block  font-medium text-[#343a40] mb-2">
            <i class="fas fa-envelope mr-2"></i>Username
          </label>
          <input
            type="text"
            id="username"
            name="username"
            value="<?= old('username') ?>"
            required
            class="input-focus border p-2 rounded-md w-full border-blue-200 outline-0"
            placeholder="Username" autocomplete="off">
        </div>

        <!-- Password Input -->
        <div>
          <label for="password" class="block  font-medium text-[#343a40] mb-2">
            <i class="fas fa-lock mr-2"></i>Password
          </label>
          <div class="relative">
            <input
              type="password"
              id="password"
              name="password"
              required
              class="input-focus border p-2 rounded-md w-full border-blue-200 outline-0"
              placeholder="••••••••" autocomplete="off">
            <button
              type="button"
              id="togglePassword"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 /60 hover: transition-colors">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <!-- Remember Me & Forgot Password
        <div class="flex items-center justify-between">
          <label class="flex items-center /80 cursor-pointer text-[#343a40]">
            <input type="checkbox" class="mr-2 rounded bg-blue-100 border-white/30 text-blue-600 focus:ring-blue-500">
            <span class="text-sm">Ingat saya</span>
          </label>

        </div> -->

        <!-- Login Button -->
        <button
          type="submit"
          class="btn-hover w-full bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
          Login
        </button>
      </form>
    </div>

    <!-- Info/Alert dari flashdata ditampilkan di atas form -->
  </div>

  <script>
    // Toggle Password Visibility (opsional, tanpa mengubah alur submit server)
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (togglePassword && passwordInput && eyeIcon) {
      togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        if (type === 'password') {
          eyeIcon.classList.remove('fa-eye-slash');
          eyeIcon.classList.add('fa-eye');
        } else {
          eyeIcon.classList.remove('fa-eye');
          eyeIcon.classList.add('fa-eye-slash');
        }
      });
    }
  </script>
</body>

</html>