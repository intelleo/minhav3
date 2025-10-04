<div class="bg-white rounded-lg shadow-md overflow-hidden">
  <div class="overflow-x-auto results-container" data-base-url="<?= site_url('Admin/MasterData/users') ?>">
    <table class="w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            <button type="button" class="sort-btn flex items-center gap-1" data-sort-by="id">
              <span>User</span>
              <span class="sort-indicator" data-for="id"></span>
            </button>
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            <button type="button" class="sort-btn flex items-center gap-1" data-sort-by="npm">
              <span>NPM</span>
              <span class="sort-indicator" data-for="npm"></span>
            </button>
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            <button type="button" class="sort-btn flex items-center gap-1" data-sort-by="jurusan">
              <span>Jurusan</span>
              <span class="sort-indicator" data-for="jurusan"></span>
            </button>
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            <button type="button" class="sort-btn flex items-center gap-1" data-sort-by="status">
              <span>Status</span>
              <span class="sort-indicator" data-for="status"></span>
            </button>
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            <button type="button" class="sort-btn flex items-center gap-1" data-sort-by="created_at">
              <span>Created</span>
              <span class="sort-indicator" data-for="created_at"></span>
            </button>
          </th>
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