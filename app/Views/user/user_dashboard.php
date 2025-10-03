<?= $this->extend('layout/usertemplate') ?>

<?= $this->section('content') ?>
<!-- 🔥 Ganti ini dengan alert reusable -->
<?= $this->include('partials/alert') ?>
<!-- card  -->
<div class="card-container">
  <div class="card">
    <img src="<?= base_url('img/rocket.svg') ?>" alt="" width="30">
    <h2>Memulai</h2>
    <p>Langkah cepat untuk mulai menggunakan Minha dengan mudah.</p>
    <div class="link-card">
      <a href="mading">selengkapnya</a>
      <i class="ri-corner-down-right-fill"></i>
    </div>
  </div>

  <div class="card ">
    <img src="<?= base_url('img/icon-chat.webp') ?>" alt="" width="30">
    <h2>Interaksi Minha</h2>
    <p>Berkomunikasi dengan AI layaknya teman diskusi kampus.</p>
    <div class="link-card">
      <a href="<?= site_url('Chatbot') ?>">selengkapnya</a>
      <i class="ri-corner-down-right-fill"></i>
    </div>
  </div>

  <!-- Card Profil (saudara dalam card-container) -->
  <div class="card">
    <h2>Profil Saya</h2>
    <p>Ringkasan informasi akun Anda.</p>
    <div class="mt-3">
      <div class="flex items-center mb-3">
        <?= $this->include('user/partials/foto_profile') ?>
        <div>
          <?php $nama = session('namalengkap'); ?>
          <p class="text-sm font-medium m-0"><?= esc($nama) ?></p>
          <p class="text-xs text-gray-600 m-0"><?= esc(session('npm')) ?></p>
        </div>
      </div>

      <div class="link-card ">
        <a href="<?= site_url('Profile') ?>">Edit profil</a>
        <i class="ri-corner-down-right-fill"></i>
      </div>
    </div>
  </div>

  <!-- Card Jam (saudara dalam card-container) -->
  <div class="card">
    <img src="<?= base_url('img/fitur.svg') ?>" alt="" width="30">
    <h2>Jam Sekarang</h2>
    <p>Waktu lokal Anda saat ini.</p>
    <div class="mt-3">
      <div id="current-time-standalone" class="text-xl font-semibold text-gray-800 tracking-wider"></div>
      <p id="current-date-standalone" class="text-xs text-gray-500 m-0"></p>
    </div>
  </div>

  <?php /* HAPUS: Jelajahi Fitur & Minha Verse & Calendar */ ?>
</div>

<!-- Overview Mading: section full width (keluar dari card) -->
<section class="my-6">
  <div class="flex justify-between items-center mb-3">
    <h2 class="font-bold text-gray-800">Mading Terbaru</h2>
    <a href="<?= site_url('Mading') ?>" class="text-xs text-[#1c68c5] hover:underline">Lihat semua</a>
  </div>

  <?php if (empty($latestMading)): ?>
    <p class="text-sm text-gray-500">Tidak ada mading.</p>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <?php foreach ($latestMading as $mading): ?>
        <div class="flex items-start bg-white rounded-lg shadow-sm p-3 border-t-3 border-[#1c68c5]">
          <div class="flex-shrink-0 w-8 h-8 rounded flex items-center justify-center mr-3"
            style="background-color: <?= mading_category_color($mading['category'], 'bg') ?>10; color: <?= mading_category_color($mading['category'], 'text') ?>;">
            <i class="<?= mading_category_icon($mading['category']) ?> text-sm"></i>
          </div>
          <div>
            <h3 class="text-sm font-medium m-0"><?= esc($mading['judul']) ?></h3>
            <p class="text-xs text-gray-600 line-clamp-1 m-0">
              <?= esc($mading['deskripsi']) ?>
            </p>
            <p class="text-xs text-gray-500 mt-1 m-0">
              <?= mading_time_ago($mading['created_at']) ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<script>
  function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const timeEl = document.getElementById('current-time-standalone');
    const dateEl = document.getElementById('current-date-standalone');
    if (timeEl) timeEl.textContent = `${hours}:${minutes}:${seconds}`;

    const months = [
      "Januari", "Februari", "Maret", "April", "Mei", "Juni",
      "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];
    if (dateEl) dateEl.textContent = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
  }

  document.addEventListener("DOMContentLoaded", () => {
    updateClock();
    setInterval(updateClock, 1000);
  });
</script>

<?= $this->endSection() ?>