<div class="bg-white rounded-lg shadow-md p-4 mb-6 flex gap-4 max-md:flex-col">
  <div class="flex flex-col md:flex-row gap-4 flex-1">
    <div class="flex-1">
      <input type="text" class="search-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Search users..." value="<?= esc($filters['search']) ?>"
        oninput="if(window.adminSearchFilter){window.adminSearchFilter.currentFilters.search=this.value;window.adminSearchFilter.currentFilters.page=1;window.adminSearchFilter.performSearch();}">
    </div>
    <div class="flex gap-4">
      <select class="status-select px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">All Status</option>
        <option value="aktif" <?= $filters['status'] == 'aktif' ? 'selected' : '' ?>>Active</option>
        <option value="pending" <?= $filters['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="nonaktif" <?= $filters['status'] == 'nonaktif' ? 'selected' : '' ?>>Inactive</option>
      </select>
      <select class="jurusan-select px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">All Jurusan</option>
        <?php foreach ($jurusanList as $jurusan): ?>
          <option value="<?= esc($jurusan) ?>" <?= $filters['jurusan'] == $jurusan ? 'selected' : '' ?>>
            <?= ucwords(str_replace('_', ' ', $jurusan)) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="flex gap-4">
    <button data-action="add-user" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
      <i class="ri-add-line mr-1 text-white"></i>
      Add User
    </button>
    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
      <i class="ri-download-line mr-1 text-white"></i>
      Export
    </button>
  </div>
</div>