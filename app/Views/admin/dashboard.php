<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-dashboard">


  <!-- Quick Stats -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6  mt-[-5rem]">
    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 rounded-lg">
            <i class="ri-team-fill text-2xl text-blue-600"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Total Users</p>
            <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_users'] ?? 0) ?></p>
          </div>
        </div>
      </div>
      <!-- Status breakdown -->
      <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="space-y-2">
          <!-- Progress bar untuk status -->
          <?php
          $totalUsers = $stats['total_users'] ?? 1; // Avoid division by zero
          $activePercentage = $totalUsers > 0 ? ($stats['active_users'] ?? 0) / $totalUsers * 100 : 0;
          $pendingPercentage = $totalUsers > 0 ? ($stats['pending_users'] ?? 0) / $totalUsers * 100 : 0;
          $inactivePercentage = $totalUsers > 0 ? ($stats['inactive_users'] ?? 0) / $totalUsers * 100 : 0;
          ?>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="flex h-2 rounded-full overflow-hidden">
              <div class="bg-green-500" style="width: <?= $activePercentage ?>%"></div>
              <div class="bg-yellow-500" style="width: <?= $pendingPercentage ?>%"></div>
              <div class="bg-red-500" style="width: <?= $inactivePercentage ?>%"></div>
            </div>
          </div>

          <!-- Status labels -->
          <div class="flex justify-between items-center text-xs">
            <div class="flex items-center">
              <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
              <span class="text-gray-600">Aktif: <?= number_format($stats['active_users'] ?? 0) ?></span>
            </div>
            <div class="flex items-center">
              <div class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></div>
              <span class="text-gray-600">Pending: <?= number_format($stats['pending_users'] ?? 0) ?></span>
            </div>
            <div class="flex items-center">
              <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
              <span class="text-gray-600">Nonaktif: <?= number_format($stats['inactive_users'] ?? 0) ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="flex items-center">
        <div class="p-3 bg-green-100 rounded-lg">
          <i class="ri-news-fill text-2xl text-green-600"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-600">Total Mading</p>
          <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_mading'] ?? 0) ?></p>
        </div>
      </div>
      <!-- Status breakdown -->
      <div class="mt-4 ">
        <div class="flex gap-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
              <span class="text-gray-600">Aktif : <span class="font-medium text-gray-900"><?= number_format($stats['mading_aktif'] ?? 0) ?></span></span>
            </div>

          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
              <span class="text-gray-600">Nonaktif : <span class="font-medium text-gray-900"><?= number_format($stats['mading_nonaktif'] ?? 0) ?></span></span>
            </div>

          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="flex items-center">
        <div class="p-3 bg-yellow-100 rounded-lg">
          <i class="ri-message-3-fill text-2xl text-yellow-600"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-600">Total Comments</p>
          <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_comments'] ?? 0) ?></p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="flex items-center">
        <div class="p-3 bg-purple-100 rounded-lg">
          <i class="ri-robot-fill text-2xl text-purple-600"></i>
        </div>
        <div class="ml-4">
          <p class="text-sm text-gray-600">Chatbot Q&A</p>
          <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['chatbot_qa'] ?? 0) ?></p>
          <p class="text-xs text-gray-500 mt-1">Coming Soon</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-md p-6 ">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
      <div class="space-y-4">
        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
          <div class="p-2 bg-blue-100 rounded-lg">
            <i class="ri-user-add-line text-blue-600"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-800">New user registered</p>
            <p class="text-xs text-gray-600">2 minutes ago</p>
          </div>
        </div>

        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
          <div class="p-2 bg-green-100 rounded-lg">
            <i class="ri-news-line text-green-600"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-800">New mading published</p>
            <p class="text-xs text-gray-600">15 minutes ago</p>
          </div>
        </div>

        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
          <div class="p-2 bg-yellow-100 rounded-lg">
            <i class="ri-message-line text-yellow-600"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-800">New comment posted</p>
            <p class="text-xs text-gray-600">1 hour ago</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
      <div class="grid grid-cols-2 gap-4">
        <a href="<?= site_url('Admin/MasterData/users') ?>" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
          <i class="ri-team-fill text-2xl text-blue-600 mb-2"></i>
          <span class="text-sm font-medium text-blue-800">Manage Users</span>
        </a>

        <a href="<?= site_url('Admin/Mading') ?>" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
          <i class="ri-news-fill text-2xl text-green-600 mb-2"></i>
          <span class="text-sm font-medium text-green-800">Manage Mading</span>
        </a>

        <a href="<?= site_url('Admin/MasterData/chatbot') ?>" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
          <i class="ri-robot-fill text-2xl text-purple-600 mb-2"></i>
          <span class="text-sm font-medium text-purple-800">Chatbot</span>
        </a>

        <a href="<?= site_url('Admin/Reports') ?>" class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors duration-200">
          <i class="ri-bar-chart-2-fill text-2xl text-yellow-600 mb-2"></i>
          <span class="text-sm font-medium text-yellow-800">Reports</span>
        </a>
      </div>
    </div>
  </div>

  <!-- System Status -->
  <div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">System Status</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-green-800">Database</p>
          <p class="text-xs text-green-600">Connected</p>
        </div>
        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
      </div>

      <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-green-800">Cache</p>
          <p class="text-xs text-green-600">Active</p>
        </div>
        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
      </div>

      <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-green-800">Storage</p>
          <p class="text-xs text-green-600">Available</p>
        </div>
        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>