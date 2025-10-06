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
        <span class="text-sm text-gray-500">Total Q&A: <?= $stats['total_chatbot'] ?></span>
        <a href="<?= site_url('Admin/MasterData/chatbot') ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
          <i class="ri-arrow-right-line mr-1 text-white"></i>
          Kelola
        </a>
      </div>
    </div>

    <!-- Mading Management Card (replace Categories) -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
      <div class="flex items-center mb-4">
        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
          <i class="ri-news-fill text-2xl text-purple-600"></i>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-800">Mading Management</h3>
          <p class="text-sm text-gray-600">Kelola postingan E-mading</p>
        </div>
      </div>
      <p class="text-gray-600 mb-4">Buat, lihat, dan atur postingan mading untuk pengguna.</p>
      <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">Total Mading: <?= $stats['total_mading'] ?></span>
        <a href="<?= site_url('Admin/MasterData/mading')  ?>" data-no-spa="true" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors duration-200">
          <i class="ri-arrow-right-line mr-1 text-white"></i>
          Kelola
        </a>
      </div>
    </div>



  </div>

  <!-- Quick Stats -->
  <div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Statistics</h3>

    <!-- Users Statistics -->
    <div class="mb-6">
      <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
        <i class="ri-team-line mr-2 text-blue-600"></i>
        Users Statistics
      </h4>
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

    <!-- Mading Statistics -->
    <div class="mb-6">
      <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
        <i class="ri-news-line mr-2 text-purple-600"></i>
        Mading Statistics
      </h4>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="text-center p-4 bg-purple-50 rounded-lg">
          <div class="text-2xl font-bold text-purple-600"><?= $stats['total_mading'] ?></div>
          <div class="text-sm text-gray-600">Total Mading</div>
        </div>
        <div class="text-center p-4 bg-green-50 rounded-lg">
          <div class="text-2xl font-bold text-green-600"><?= $stats['active_mading'] ?></div>
          <div class="text-sm text-gray-600">Active Mading</div>
        </div>
        <div class="text-center p-4 bg-yellow-50 rounded-lg">
          <div class="text-2xl font-bold text-yellow-600"><?= $stats['pending_mading'] ?></div>
          <div class="text-sm text-gray-600">Pending Mading</div>
        </div>
        <div class="text-center p-4 bg-red-50 rounded-lg">
          <div class="text-2xl font-bold text-red-600"><?= $stats['inactive_mading'] ?></div>
          <div class="text-sm text-gray-600">Inactive Mading</div>
        </div>
      </div>
    </div>

    <!-- Chatbot Statistics -->
    <div>
      <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
        <i class="ri-robot-line mr-2 text-green-600"></i>
        Chatbot Statistics
      </h4>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="text-center p-4 bg-green-50 rounded-lg">
          <div class="text-2xl font-bold text-green-600"><?= $stats['total_chatbot'] ?></div>
          <div class="text-sm text-gray-600">Total Q&A</div>
        </div>
        <div class="text-center p-4 bg-blue-50 rounded-lg">
          <div class="text-2xl font-bold text-blue-600"><?= $stats['akademik_chatbot'] ?></div>
          <div class="text-sm text-gray-600">Akademik</div>
        </div>
        <div class="text-center p-4 bg-purple-50 rounded-lg">
          <div class="text-2xl font-bold text-purple-600"><?= $stats['administrasi_chatbot'] ?></div>
          <div class="text-sm text-gray-600">Administrasi</div>
        </div>
        <div class="text-center p-4 bg-orange-50 rounded-lg">
          <div class="text-2xl font-bold text-orange-600"><?= $stats['umum_chatbot'] ?></div>
          <div class="text-sm text-gray-600">Umum</div>
        </div>
      </div>
    </div>
  </div>

</div>

<?= $this->endSection() ?>