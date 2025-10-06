<?= $this->extend('layout/usertemplate') ?>

<?= $this->section('content') ?>
<!-- 🔥 Ganti ini dengan alert reusable -->
<?= $this->include('partials/alert') ?>
<!-- card dans shortcut -->

<!-- Shortcut Cepat: grid kartu dinamis untuk akses fitur utama -->
<section class="mt-[-5rem]">
  <?php
  $shortcuts = [
    [
      'label' => 'Mading',
      'href'  => site_url('Mading'),
      'icon'  => 'ri-newspaper-line',
      'bg'    => 'bg-blue-50',
      'fg'    => 'text-[#1c68c5]'
    ],
    [
      'label' => 'Notifikasi',
      'href'  => site_url('Notifications'),
      'icon'  => 'ri-notification-3-line',
      'bg'    => 'bg-amber-50',
      'fg'    => 'text-amber-600'
    ],
    [
      'label' => 'Like Saya',
      'href'  => site_url('Likes'),
      'icon'  => 'ri-heart-3-line',
      'bg'    => 'bg-rose-50',
      'fg'    => 'text-rose-600'
    ],
    [
      'label' => 'Chatbot',
      'href'  => site_url('Chatbot'),
      'icon'  => 'ri-robot-line',
      'bg'    => 'bg-indigo-50',
      'fg'    => 'text-indigo-600'
    ],
    [
      'label' => 'Profil',
      'href'  => site_url('Profile'),
      'icon'  => 'ri-user-3-line',
      'bg'    => 'bg-slate-50',
      'fg'    => 'text-slate-700'
    ],
  ];
  ?>



  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
    <?php foreach ($shortcuts as $s): ?>
      <a href="<?= $s['href'] ?>" class="group relative overflow-hidden rounded-lg border border-gray-100 bg-white p-4 shadow hover:shadow-md transition">
        <div class="flex items-center gap-3">
          <div class="w-10 h-14 rounded-md flex items-center justify-center <?= $s['bg'] ?>">
            <i class="<?= $s['icon'] ?> text-lg <?= $s['fg'] ?>"></i>
          </div>
          <div>
            <div class="text-sm font-semibold text-gray-800 group-hover:underline">
              <?= esc($s['label']) ?>
            </div>
            <div class="text-[11px] text-gray-500">Akses cepat</div>
          </div>
        </div>
        <i class="ri-arrow-right-up-line absolute right-3 top-3 text-gray-300 group-hover:text-gray-400"></i>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- Overview Mading: section full width (keluar dari card), improved UI -->
<section class="my-6">
  <div class="flex justify-between items-center mb-3">
    <h2 class="font-bold text-gray-800">Mading Terbaru</h2>
    <a href="<?= site_url('Mading') ?>" class="text-xs text-[#1c68c5] hover:underline">Lihat semua</a>
  </div>

  <?php if (empty($latestMading)): ?>
    <p class="text-sm text-gray-500">Tidak ada mading.</p>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <?php foreach ($latestMading as $mading): ?>
        <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center">
              <div class="w-8 h-8 rounded flex items-center justify-center mr-2"
                style="background-color: <?= mading_category_color($mading['category'], 'bg') ?>10; color: <?= mading_category_color($mading['category'], 'text') ?>;">
                <i class="<?= mading_category_icon($mading['category']) ?> text-sm"></i>
              </div>
              <span class="text-xs text-gray-600 capitalize"><?= esc($mading['category']) ?></span>
            </div>
            <span class="text-[10px] text-gray-500"><?= mading_time_ago($mading['created_at']) ?></span>
          </div>
          <h3 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2"><?= esc($mading['judul']) ?></h3>
          <p class="text-xs text-gray-600 mb-3 line-clamp-2"><?= esc($mading['deskripsi']) ?></p>
          <div class="flex items-center text-[11px] text-gray-600 space-x-3">
            <span class="flex items-center"><i class="ri-eye-line mr-1"></i><?= (int)($mading['views'] ?? 0) ?></span>
            <span class="flex items-center"><i class="ri-heart-line mr-1"></i><?= (int)($mading['total_likes'] ?? 0) ?></span>
            <span class="flex items-center"><i class="ri-message-3-line mr-1"></i><?= (int)($mading['total_comments'] ?? 0) ?></span>
          </div>
          <div class="mt-3">
            <a href="<?= site_url('Mading/detail/' . $mading['id']) ?>" class="inline-flex items-center text-xs text-[#1c68c5] hover:underline">
              Baca selengkapnya <i class="ri-arrow-right-line ml-1"></i>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>



<?= $this->endSection() ?>