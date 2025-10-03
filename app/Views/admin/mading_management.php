<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-mading-management">
  <!-- Header Section -->
  <div class="mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">E-mading Management</h2>
        <p class="text-gray-600">Kelola konten mading</p>
      </div>
      <div class="flex gap-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
          <i class="ri-add-line mr-1"></i>
          Add Mading
        </button>
      </div>
    </div>
  </div>

  <!-- Coming Soon -->
  <div class="bg-white rounded-lg shadow-md p-12 text-center">
    <i class="ri-news-line text-6xl text-gray-400 mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-800 mb-2">E-mading Management</h3>
    <p class="text-gray-600 mb-4">Fitur ini akan segera hadir</p>
    <p class="text-sm text-gray-500">Kami sedang mengembangkan fitur manajemen mading yang lengkap</p>
  </div>

</div>

<?= $this->endSection() ?>