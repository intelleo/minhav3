<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-master-data mt-[-5rem]">
  <!-- Header Section -->
  <!-- Master Data Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Users Management Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
      <div class="flex items-center mb-4">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
          <i class="ri-team-fill text-2xl text-blue-600"></i>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-800">Users Management</h3>
          <p class="text-sm text-gray-600">Kelola data pengguna</p>
        </div>
      </div>
      <p class="text-gray-600 mb-4">Kelola data pengguna sistem, status akun, dan informasi pengguna.</p>
      <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">Total Users: <?= $stats['total_users'] ?></span>
        <a href="<?= site_url('Admin/MasterData/users') ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
          <i class="ri-arrow-right-line mr-1 text-white"></i>
          Kelola
        </a>
      </div>
    </div>

    <!-- Chatbot Management Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
      <div class="flex items-center mb-4">
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
          <i class="ri-robot-fill text-2xl text-green-600"></i>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-800">Chatbot Management</h3>
          <p class="text-sm text-gray-600">Kelola data chatbot</p>
        </div>
      </div>
      <p class="text-gray-600 mb-4">Kelola pertanyaan dan jawaban chatbot, kategori, dan training data.</p>
      <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">Total Q&A: 0</span>
        <a href="<?= site_url('Admin/MasterData/chatbot') ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
          <i class="ri-arrow-right-line mr-1 text-white"></i>
          Kelola
        </a>
      </div>
    </div>

    <!-- Categories Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
      <div class="flex items-center mb-4">
        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
          <i class="ri-folder-fill text-2xl text-purple-600"></i>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-800">Categories</h3>
          <p class="text-sm text-gray-600">Kelola kategori</p>
        </div>
      </div>
      <p class="text-gray-600 mb-4">Kelola kategori mading dan chatbot untuk organisasi konten.</p>
      <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">Total Categories: 0</span>
        <a href="#" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors duration-200">
          <i class="ri-arrow-right-line mr-1"></i>
          Kelola
        </a>
      </div>
    </div>



  </div>

  <!-- Quick Stats -->
  <div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Statistics</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="text-center p-4 bg-blue-50 rounded-lg">
        <div class="text-2xl font-bold text-blue-600"><?= $stats['total_users'] ?></div>
        <div class="text-sm text-gray-600">Total Users</div>
      </div>
      <div class="text-center p-4 bg-green-50 rounded-lg">
        <div class="text-2xl font-bold text-green-600"><?= $stats['active_users'] ?></div>
        <div class="text-sm text-gray-600">Active Users</div>
      </div>
      <div class="text-center p-4 bg-yellow-50 rounded-lg">
        <div class="text-2xl font-bold text-yellow-600"><?= $stats['pending_users'] ?></div>
        <div class="text-sm text-gray-600">Pending Users</div>
      </div>
      <div class="text-center p-4 bg-red-50 rounded-lg">
        <div class="text-2xl font-bold text-red-600"><?= $stats['inactive_users'] ?></div>
        <div class="text-sm text-gray-600">Inactive Users</div>
      </div>
    </div>
  </div>

</div>

<?= $this->endSection() ?>