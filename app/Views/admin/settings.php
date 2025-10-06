<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-settings mt-[-5rem]">


  <!-- Activity Logs -->
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-800">Activity Logs</h3>
      <form method="get" class="flex items-center space-x-2">
        <select name="type" class="border border-gray-300 rounded px-2 py-1 text-sm">
          <option value="" <?= empty($filters['type']) ? 'selected' : '' ?>>Semua Tipe</option>
          <option value="user" <?= ($filters['type'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
          <option value="mading" <?= ($filters['type'] ?? '') === 'mading' ? 'selected' : '' ?>>Mading</option>
          <option value="comment" <?= ($filters['type'] ?? '') === 'comment' ? 'selected' : '' ?>>Komentar</option>
        </select>
        <input type="date" name="from" value="<?= esc($filters['from'] ?? '') ?>" class="border border-gray-300 rounded px-2 py-1 text-sm" />
        <input type="date" name="to" value="<?= esc($filters['to'] ?? '') ?>" class="border border-gray-300 rounded px-2 py-1 text-sm" />
        <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded">Filter</button>
      </form>
    </div>

    <?php if (empty($logs)): ?>
      <div class="text-sm text-gray-500">Belum ada aktivitas.</div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($logs as $row): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <?= ucfirst(esc($row['type'])) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <?= esc($row['title']) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <?= esc($row['detail']) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="pagination-container">
        <?= view('admin/partials/reports_pagination', ['pagination' => $pagination]) ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<?= $this->endSection() ?>