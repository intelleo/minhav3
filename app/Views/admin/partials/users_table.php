<div class="bg-white rounded-lg shadow-md overflow-hidden">
  <div class="overflow-x-auto results-container" data-base-url="<?= site_url('Admin/MasterData/users') ?>">
    <table class="w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NPM</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200" id="users-tbody">
        <?= view('admin/partials/users_tbody', ['users' => $users]) ?>
      </tbody>
    </table>
  </div>
</div>

<div class="pagination-container">
  <?= view('admin/partials/users_pagination', [
    'pagination' => $pagination,
    'filters' => $filters
  ]) ?>
</div>