<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <meta name="base-url" content="<?= rtrim(site_url('/'), '/') ?>">
  <title>Admin Panel - <?= esc($title ?? 'Dashboard') ?></title>
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
        <p>Admin</p>
      </div>

      <!-- sidebar btn -->
      <button>
        <?= img_tag('img/sidebar.svg', 'logo-show', ['id' => 'btnSidebar', 'width' => 40, 'height' => 40]) ?>
        <?= img_tag('img/icon-chat.webp', 'logo', ['class' => 'hidden', 'id' => 'logoCollapsed', 'width' => 40, 'height' => 40]) ?>
      </button>
    </div>
    <nav>
      <a href="<?= site_url('/Admin/Dashboard') ?>" class="site <?= (uri_string() == 'Admin/Dashboard' || uri_string() == 'Admin/Dashboard') ? 'aktif' : '' ?>">
        <i class="ri-dashboard-fill text-lg"></i>
        <span>Dashboard</span>
      </a>

      <a href="<?= site_url('Admin/MasterData') ?>" class="site <?= (strpos(uri_string(), 'Admin/MasterData') === 0) ? 'aktif' : '' ?>">
        <i class="ri-database-2-fill"></i>
        <span>Master Data</span>
      </a>

      <a href="<?= site_url('Admin/Mading') ?>" class="site <?= (strpos(uri_string(), 'Admin/Mading') === 0) ? 'aktif' : '' ?>">
        <i class="ri-news-fill"></i>
        <span>E-mading</span>
      </a>

      <a href="<?= site_url('Admin/Reports') ?>" class="site <?= (strpos(uri_string(), 'Admin/Reports') === 0) ? 'aktif' : '' ?>">
        <i class="ri-bar-chart-2-fill"></i>
        <span>Laporan</span>
      </a>

      <a href="<?= site_url('Admin/ActivityLogs') ?>" class="site <?= (strpos(uri_string(), 'Admin/ActivityLogs') === 0 || strpos(uri_string(), 'Admin/Settings') === 0) ? 'aktif' : '' ?>">
        <i class="ri-list-unordered"></i>
        <span>Activity Logs</span>
      </a>

      <a href="<?= site_url('admin/logout') ?>" class="site">
        <i class="ri-logout-circle-r-fill"></i>
        <span>Logout</span>
      </a>
    </nav>

  </aside>
  <!-- mobile navbar -->
  <header class="mobile-navbar hidden max-lg:block fixed top-0 left-0 w-full bg-white shadow-md z-[9999] py-3">
    <div class="logo-btn flex justify-between items-center px-4">
      <div class="logo-mobile flex items-center gap-2 z-20">
        <?= img_tag('img/icon-chat.webp', 'logo', ['width' => 40, 'height' => 40]) ?>
        <p class="text-[#247de3] font-bold">Admin</p>
      </div>
      <button class="btn-nav"><?= img_tag('img/sidebar.svg', 'sidebar', ['width' => 40, 'height' => 40]) ?></button>
    </div>
    <nav class="nav-mobile absolute top-0 w-[80%] bg-white shadow-2xl z-10 p-4 h-screen">
      <ul class="flex flex-col gap-4 mt-14">
        <a href="<?= site_url('/Admin') ?>" class="site py-2 border-b border-blue-50 px-2 <?= (uri_string() == 'Admin' || uri_string() == 'Admin/') ? 'aktif' : '' ?>">
          <i class="ri-dashboard-fill"></i>
          <span>Dashboard</span>
        </a>

        <a href="<?= site_url('Admin/MasterData') ?>" class="site py-2 border-b border-blue-50 px-2 <?= (strpos(uri_string(), 'Admin/MasterData') === 0) ? 'aktif' : '' ?>">
          <i class="ri-database-2-fill"></i>
          <span>Master Data</span>
        </a>

        <a href="<?= site_url('Admin/Mading') ?>" class="site py-2 border-b border-blue-50 px-2 <?= (strpos(uri_string(), 'Admin/Mading') === 0) ? 'aktif' : '' ?>">
          <i class="ri-news-fill"></i>
          <span>E-mading</span>
        </a>

        <a href="<?= site_url('Admin/Reports') ?>" class="site py-2 border-b border-blue-50 px-2 <?= (strpos(uri_string(), 'Admin/Reports') === 0) ? 'aktif' : '' ?>">
          <i class="ri-bar-chart-2-fill"></i>
          <span>Laporan</span>
        </a>

        <a href="<?= site_url('Admin/Settings') ?>" class="site py-2 border-b border-blue-50 px-2 <?= (strpos(uri_string(), 'Admin/Settings') === 0) ? 'aktif' : '' ?>">
          <i class="ri-settings-3-fill"></i>
          <span>Settings</span>
        </a>

        <a href="<?= site_url('admin/logout') ?>" class="site py-2 border-b border-blue-50 px-2">
          <i class="ri-logout-circle-r-fill"></i>
          <span>Logout</span>
        </a>
      </ul>
    </nav>
  </header>

  <!-- Main Content: Scrollable, isi halaman -->
  <main class="">

    <!-- Page Content -->
    <div class="content ">
      <div class="judul max-lg:mt-[3.5rem]">
        <h1 class=""><?= esc($title ?? 'Admin Dashboard') ?></h1>
        <p class="">Selamat datang, <?= esc(session('admin_username') ?? 'Admin') ?>!</p>
      </div>
      <?php if (isset($view_content)): ?>
        <?= $this->include($view_content) ?>
      <?php else: ?>
        <?= $this->renderSection('content') ?>
      <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-8 text-sm text-gray-500 pb-4">
      &copy; <?= date('Y') ?> Minha Admin. All rights reserved.
    </footer>
  </main>

  <!-- Global scripts -->
  <script src="<?= base_url('vendor/axios.min.js') ?>"></script>
  <script src="<?= base_url('js/axios-setup.js') ?>"></script>
  <script src="<?= base_url('js/alerts.js') ?>"></script>
  <script src="<?= base_url('js/admin-spa-router.js') ?>"></script>

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

      // mobile navbar
      const btnNav = document.querySelector('.btn-nav');
      const navMobile = document.querySelector('.nav-mobile');
      if (btnNav && navMobile) {
        btnNav.addEventListener('click', function() {
          navMobile.classList.toggle('nav-show');
        });

        // Tutup menu mobile saat item navigasi diklik
        const navLinks = navMobile.querySelectorAll('a');
        navLinks.forEach(function(link) {
          link.addEventListener('click', function() {
            navMobile.classList.remove('nav-show');
          });
        });
      }
    });
  </script>

</body>
<script src="<?= base_url('vendor/axios.min.js') ?>"></script>
<script src="<?= base_url('js/axios-setup.js') ?>"></script>

</html>