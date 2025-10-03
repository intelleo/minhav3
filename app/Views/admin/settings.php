<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-settings">
  <!-- Header Section -->
  <div class="mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Settings</h2>
        <p class="text-gray-600">Pengaturan sistem</p>
      </div>
    </div>
  </div>

  <!-- Coming Soon -->
  <div class="bg-white rounded-lg shadow-md p-12 text-center">
    <i class="ri-settings-3-line text-6xl text-gray-400 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-800 mb-2">Settings</h3>
    <p class="text-gray-600 mb-4">Fitur ini akan segera hadir</p>
    <p class="text-sm text-gray-500">Kami sedang mengembangkan panel pengaturan yang lengkap</p>
  </div>

</div>

<?= $this->endSection() ?>