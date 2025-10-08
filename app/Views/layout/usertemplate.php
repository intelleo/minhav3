<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <meta name="base-url" content="<?= rtrim(site_url('/'), '/') ?>">
  <title>Minha - <?= esc($title ?? 'Dashboard') ?></title>

  <!-- Tailwind CSS -->
  <link rel="stylesheet" href="<?= base_url('css/output.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/custom.css') ?>">
  <link rel="stylesheet" href="<?= base_url('fonts/remixicon.css') ?>">

  <!-- PWA -->
  <meta name="theme-color" content="#247de3">
  <link rel="manifest" href="<?= base_url('manifest.webmanifest') ?>">
  <link rel="apple-touch-icon" href="<?= base_url('img/icon-chat.png') ?>">

  <!-- Fonts -->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
  </style>
</head>

<body class="user-template">
  <aside>
    <div class="logo-user-template">
      <div class="logo-text">
        <?= img_tag('img/icon-chat.webp', 'logo-minha', ['id' => 'logoDesktop', 'class' => 'logo', 'width' => 40, 'height' => 40, 'loading' => 'eager', 'decoding' => 'sync', 'fetchpriority' => 'high']) ?>
        <p>Minha</p>
      </div>

      <!-- sidebar btn -->
      <button>
        <?= img_tag('img/sidebar.svg', 'logo-show', ['id' => 'btnSidebar', 'width' => 40, 'height' => 40]) ?>
        <?= img_tag('img/icon-chat.webp', 'logo', ['class' => 'hidden', 'id' => 'logoCollapsed', 'width' => 40, 'height' => 40]) ?>
      </button>
    </div>
    <nav>
      <a href="<?= site_url('Dashboard') ?>" class="site <?= current_url() == site_url('Dashboard') ? 'aktif' : '' ?>">
        <i class="ri-home-6-fill text-lg"></i>
        <span>Dashboard</span>
      </a>

      <a href="<?= site_url('Mading') ?>"
        class="site <?= (strpos(current_url(), site_url('Mading')) === 0) ? 'aktif' : '' ?>">
        <i class="ri-news-fill"></i>
        <span>Mading Online</span>
      </a>
      <div class="chatbot">
        <a href="<?= site_url('Chatbot') ?>" class="site <?= current_url() == site_url('Chatbot') ? 'aktif' : '' ?>">
          <i class="ri-chat-ai-fill"></i>
          <span>Chatbot Minha</span>
        </a>
      </div>
      <a href="<?= site_url('Profile') ?>" class="site relative <?= ((strpos(current_url(), site_url('Profile')) === 0) || (strpos(current_url(), site_url('Likes')) === 0) || (strpos(current_url(), site_url('Notifications')) === 0)) ? 'aktif' : '' ?>">
        <i class="ri-account-circle-fill"></i>
        <span>Profile Saya</span>
        <span id="navNotifDot" class="absolute right-2 top-2 w-2 h-2 bg-red-500 rounded-full hidden"></span>
      </a>

      <a href="<?= site_url('logout') ?>" class="site">
        <i class="ri-logout-circle-r-fill"></i>
        <span>Logout</span>
      </a>
    </nav>

  </aside>
  <header class="mobile-navbar hidden max-lg:block fixed top-0 left-0 w-full bg-white shadow-md z-[9999] py-3 ">
    <div class="logo-btn flex justify-between items-center px-4">
      <div class="logo-mobile flex items-center gap-2 z-20">
        <?= img_tag('img/icon-chat.webp', 'logo', ['width' => 30, 'height' => 30]) ?>
        <p class="text-[#247de3] font-bold">Minha</p>
      </div>
    </div>
  </header>
  <!-- Mobile Bottom Navigation (Instagram Style) -->
  <nav class="mobile-bottom-nav hidden max-lg:flex fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 z-[9999]">
    <a href="<?= site_url('Dashboard') ?>" class="nav-item flex-1 flex flex-col items-center justify-center py-2 px-1 <?= (strpos(current_url(), site_url('Dashboard')) === 0) ? 'active' : '' ?>">
      <i class="ri-home-6-fill text-xl mb-1"></i>
      <span class="text-xs">Home</span>
    </a>

    <a href="<?= site_url('Mading') ?>" class="nav-item flex-1 flex flex-col items-center justify-center py-2 px-1 <?= (strpos(current_url(), site_url('Mading')) === 0) ? 'active' : '' ?>">
      <i class="ri-news-fill text-xl mb-1"></i>
      <span class="text-xs">Mading</span>
    </a>

    <a href="<?= site_url('Chatbot') ?>" class="nav-item flex-1 flex flex-col items-center justify-center py-2 px-1 <?= (strpos(current_url(), site_url('Chatbot')) === 0) ? 'active' : '' ?>">
      <i class="ri-chat-ai-fill text-xl mb-1"></i>
      <span class="text-xs">Chat</span>
    </a>

    <a href="<?= site_url('Profile') ?>" class="nav-item flex-1 flex flex-col items-center justify-center py-2 px-1 relative <?= ((strpos(current_url(), site_url('Profile')) === 0) || (strpos(current_url(), site_url('Likes')) === 0) || (strpos(current_url(), site_url('Notifications')) === 0)) ? 'active' : '' ?>">
      <i class="ri-account-circle-fill text-xl mb-1"></i>
      <span class="text-xs">Profile</span>
      <span id="navNotifDotMobile" class="absolute top-1 right-4 w-2 h-2 bg-red-500 rounded-full hidden"></span>
    </a>

    <a href="<?= site_url('logout') ?>" class="nav-item flex-1 flex flex-col items-center justify-center py-2 px-1" data-no-spa="true">
      <i class="ri-logout-circle-r-fill text-xl mb-1"></i>
      <span class="text-xs">Logout</span>
    </a>
  </nav>

  <!-- Main Content: Scrollable, isi halaman -->
  <main class="max-lg:pb-20">

    <!-- Page Content -->
    <div class="content">
      <div class="judul max-lg:mt-[3.5rem]">
        <h1 class=""><?= esc($title) ?></h1>
        <p class="">Selamat datang, <?= esc(session('namalengkap')) ?>!</p>
      </div>
      <?= $this->renderSection('content') ?>

    </div>

    <!-- Footer -->
    <footer class="text-center mt-8 text-sm text-gray-500 pb-4">
      &copy; <?= date('Y') ?> Minha. All rights reserved.
    </footer>
  </main>

  <!-- Global scripts -->
  <script src="<?= base_url('vendor/axios.min.js') ?>"></script>
  <script src="<?= base_url('js/axios-setup.js') ?>"></script>
  <script src="<?= base_url('js/alerts.js') ?>"></script>
  <script src="<?= base_url('js/spa-router.js') ?>"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Register Service Worker
      if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('<?= base_url('sw.js') ?>').catch(() => {});
      }

      // Install prompt handling
      let deferredPrompt;
      const installBtnId = 'pwaInstallBtn';

      function ensureInstallButton() {
        if (document.getElementById(installBtnId)) return document.getElementById(installBtnId);
        const btn = document.createElement('button');
        btn.id = installBtnId;
        btn.textContent = 'Install Minha';
        btn.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded shadow hidden';
        document.body.appendChild(btn);
        return btn;
      }

      window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        const btn = ensureInstallButton();
        btn.classList.remove('hidden');
        btn.addEventListener('click', async () => {
          btn.classList.add('hidden');
          if (!deferredPrompt) return;
          deferredPrompt.prompt();
          const {
            outcome
          } = await deferredPrompt.userChoice;
          deferredPrompt = null;
        }, {
          once: true
        });
      });

      window.addEventListener('appinstalled', () => {
        const btn = document.getElementById(installBtnId);
        if (btn) btn.classList.add('hidden');
      });
      const sidebarBtn = document.querySelector('.logo-user-template button');
      const sidebar = document.querySelector('aside');
      const mainContent = document.querySelector('main');
      const imgDekstop = document.querySelector('.logo');
      const btnDesktop = document.querySelector('.logo-user-template');
      const logoCollapsed = document.getElementById('logoCollapsed');
      const btnSidebar = document.getElementById('btnSidebar');

      sidebarBtn.addEventListener('click', function() {
        sidebar.classList.toggle('sidebar-collapsed');
        sidebar.classList.toggle('aside-collapsed');
        // mainContent.classList.toggle('main-expanded');
        mainContent.classList.toggle('main-width');
        imgDekstop.classList.toggle('hidden');
        btnDesktop.classList.toggle('center');
        btnSidebar.classList.toggle('hidden');
        logoCollapsed.classList.toggle('hidden');
      });

      // Mobile bottom navigation - no additional JS needed

      // Fetch notifikasi count untuk menampilkan dot di navbar & card Profile
      window.__refreshNotifCount = function() {
        try {
          // Hanya jalankan jika user sudah login (ada session logged_in)
          <?php if (session()->has('logged_in')): ?>
            const http = (window.api || window.axios || null);
            if (http) {
              const url = '<?= site_url('Notifications/count') ?>' + '?_ts=' + Date.now();
              http.get(url, {
                headers: {
                  'Cache-Control': 'no-cache'
                }
              }).then(function(res) {
                var count = (res && res.data && res.data.count) ? parseInt(res.data.count) : 0;
                var dot = document.getElementById('navNotifDot');
                var dotMobile = document.getElementById('navNotifDotMobile');
                var cardDot = document.getElementById('cardNotifDot');
                if (count > 0) {
                  if (dot) dot.classList.remove('hidden');
                  if (dotMobile) dotMobile.classList.remove('hidden');
                  if (cardDot) cardDot.classList.remove('hidden');
                } else {
                  if (dot) dot.classList.add('hidden');
                  if (dotMobile) dotMobile.classList.add('hidden');
                  if (cardDot) cardDot.classList.add('hidden');
                }
              }).catch(function(_) {});
            }
          <?php endif; ?>
        } catch (_) {}
      };
      window.__refreshNotifCount();
    });
  </script>


</body>
<script src="<?= base_url('vendor/axios.min.js') ?>"></script>
<script src="<?= base_url('js/axios-setup.js') ?>"></script>

</html>