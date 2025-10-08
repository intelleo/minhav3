<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-dashboard mt-[-5rem]">


  <!-- Quick Stats -->
  <div class=" bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Statistics</h3>

    <!-- Users Statistics -->
    <div class="mb-6">
      <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
        <i class="ri-team-line mr-2 text-blue-600"></i>
        Users Statistics
      </h4>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="text-center p-4 bg-blue-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-blue-600"><?= $stats['total_users'] ?></div>
          <div class="text-sm text-gray-600">Total Users</div>
        </div>
        <div class="text-center p-4 bg-green-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-green-600"><?= $stats['active_users'] ?></div>
          <div class="text-sm text-gray-600">Active Users</div>
        </div>
        <div class="text-center p-4 bg-yellow-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-yellow-600"><?= $stats['pending_users'] ?></div>
          <div class="text-sm text-gray-600">Pending Users</div>
        </div>
        <div class="text-center p-4 bg-red-50 rounded-lg shadow">
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
        <div class="text-center p-4 bg-purple-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-purple-600"><?= $stats['total_mading'] ?></div>
          <div class="text-sm text-gray-600">Total Mading</div>
        </div>
        <div class="text-center p-4 bg-green-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-green-600"><?= $stats['active_mading'] ?></div>
          <div class="text-sm text-gray-600">Active Mading</div>
        </div>
        <div class="text-center p-4 bg-yellow-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-yellow-600"><?= $stats['pending_mading'] ?></div>
          <div class="text-sm text-gray-600">Pending Mading</div>
        </div>
        <div class="text-center p-4 bg-red-50 rounded-lg shadow">
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
        <div class="text-center p-4 bg-green-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-green-600"><?= $stats['total_chatbot'] ?></div>
          <div class="text-sm text-gray-600">Total Q&A</div>
        </div>
        <div class="text-center p-4 bg-blue-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-blue-600"><?= $stats['akademik_chatbot'] ?></div>
          <div class="text-sm text-gray-600">Akademik</div>
        </div>
        <div class="text-center p-4 bg-purple-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-purple-600"><?= $stats['administrasi_chatbot'] ?></div>
          <div class="text-sm text-gray-600">Administrasi</div>
        </div>
        <div class="text-center p-4 bg-orange-50 rounded-lg shadow">
          <div class="text-2xl font-bold text-orange-600"><?= $stats['umum_chatbot'] ?></div>
          <div class="text-sm text-gray-600">Umum</div>
        </div>
      </div>
    </div>
  </div>



  <!-- Quick Actions -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow p-6 ">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
      <?php if (empty($recent)): ?>
        <div class="text-sm text-gray-500">Belum ada aktivitas terbaru.</div>
      <?php else: ?>
        <div class="space-y-3">
          <?php foreach (array_slice($recent, 0, 5) as $item): ?>
            <?php
            $iconMap = [
              'blue' => 'bg-blue-100 text-blue-600',
              'green' => 'bg-green-100 text-green-600',
              'yellow' => 'bg-yellow-100 text-yellow-600',
            ];
            $cls = $iconMap[$item['color']] ?? 'bg-gray-100 text-gray-600';
            ?>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
              <div class="p-2 rounded-lg <?= $cls ?>">
                <i class="<?= esc($item['icon']) ?>"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-800"><?= esc($item['title']) ?></p>
                <p class="text-xs text-gray-600"><?= esc($item['detail']) ?> • <?= date('d M Y H:i', strtotime($item['created_at'])) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
          <div class="pt-2">
            <a href="<?= site_url('Admin/ActivityLogs') ?>" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
              Lihat semua aktivitas
              <i class="ri-arrow-right-line ml-1"></i>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
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
  <div class="mt-8 bg-white rounded-lg shadow p-6">
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