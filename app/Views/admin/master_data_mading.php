<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-mading-management mt-[-5rem]">
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="text-center p-4 bg-white shadow-md rounded-lg">
      <div class="text-2xl font-bold text-purple-600"><?= (int)($stats['total'] ?? 0) ?></div>
      <div class="text-sm text-gray-600">Total</div>
    </div>
    <div class="text-center p-4 bg-white shadow-md rounded-lg">
      <div class="text-2xl font-bold text-green-600"><?= (int)($stats['aktif'] ?? 0) ?></div>
      <div class="text-sm text-gray-600">Aktif</div>
    </div>
    <div class="text-center p-4 bg-white shadow-md rounded-lg">
      <div class="text-2xl font-bold text-yellow-600"><?= (int)($stats['pending'] ?? 0) ?></div>
      <div class="text-sm text-gray-600">Pending</div>
    </div>
    <div class="text-center p-4 bg-white shadow-md rounded-lg">
      <div class="text-2xl font-bold text-red-600"><?= (int)($stats['nonaktif'] ?? 0) ?></div>
      <div class="text-sm text-gray-600">Nonaktif</div>
    </div>
  </div>

  <div class="flex gap-3 items-center justify-center mb-4 h-20 bg-white px-9 py-2 rounded-lg shadow">
    <div class=" grid grid-cols-1 md:grid-cols-3 gap-3">
      <input type="text" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari judul/desk..." class="search-input px-3 py-2 border border-gray-200 rounded-md outline-0">
      <select class="status-select px-3 py-2 border rounded-md border-gray-200">
        <option value="">Semua Status</option>
        <?php foreach (['pending', 'aktif', 'nonaktif'] as $st): ?>
          <option value="<?= $st ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
        <?php endforeach; ?>
      </select>
      <select class="category-select px-3 py-2 border rounded-md border-gray-200">
        <option value="">Semua Kategori</option>
        <?php foreach (($categories ?? []) as $cat): ?>
          <option value="<?= $cat ?>" <?= ($filters['category'] ?? '') === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="button" data-action="add-mading" class="bg-purple-600 text-white p-2 rounded-lg hover:bg-purple-700 transition-colors duration-200 h-[38px] whitespace-nowrap px-4">
      <i class="ri-add-line mr-1 text-white"></i> Tambah Mading
    </button>
  </div>

  <div class="overflow-x-auto bg-white rounded-lg shadow results-container" data-base-url="<?= site_url('Admin/MasterData/mading') ?>">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left">Judul</th>
          <th class="px-4 py-3 text-left">Kategori</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Periode</th>
          <th class="px-4 py-3 text-left">Dibuat</th>
          <th class="px-4 py-3 text-left">Gambar</th>
        </tr>
      </thead>
      <tbody id="users-tbody">
        <?= view('admin/partials/mading_tbody', ['mading' => $mading]) ?>
      </tbody>
    </table>
  </div>

  <div class="pagination-container mt-4 flex justify-end gap-2">
    <?= view('admin/partials/mading_pagination', ['pagination' => $pagination, 'filters' => $filters]) ?>
  </div>
</div>
<script src="<?= base_url('js/admin-search-filter.js') ?>"></script>
<script>
  (function() {
    try {
      window.__DISABLE_ADMIN_SEARCH_AUTOINIT = true;
      if (window.AdminSearchFilter) {
        window.adminSearchFilter = null;
        window.adminSearchFilter = new AdminSearchFilter({
          baseUrl: '<?= site_url('Admin/MasterData/mading') ?>',
          searchInput: '.search-input',
          statusSelect: '.status-select',
          jurusanSelect: '.jurusan-select',
          categorySelect: '.category-select',
          resultsContainer: '.results-container',
          paginationContainer: '.pagination-container',
          debounceDelay: 400,
          autoLoad: false
        });
      }

      // Fallback listener khusus kategori jika delegasi tidak terpasang
      var catSel = document.querySelector('.category-select');
      if (catSel) {
        catSel.addEventListener('change', function(e) {
          if (window.adminSearchFilter && typeof window.adminSearchFilter.setFilter === 'function') {
            window.adminSearchFilter.setFilter('category', e.target.value || '');
          }
        });
      }

      if (window.madingPaginationHandler) {
        document.removeEventListener('click', window.madingPaginationHandler);
      }
      window.madingPaginationHandler = function(e) {
        var link = e.target.closest('.pagination-link');
        if (!link) return;
        e.preventDefault();
        var page = parseInt(link.getAttribute('data-page'));
        if (page && window.adminSearchFilter) {
          window.adminSearchFilter.currentFilters.page = page;
          window.adminSearchFilter.performSearch();
        }
      };
      document.addEventListener('click', window.madingPaginationHandler);
    } catch (e) {
      console.error(e);
    }
  })();
</script>

<!-- Modal Tambah Mading -->
<div id="modalAddMading" class="fixed overflow-y-auto inset-0 bg-black/40 hidden z-50">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-[38rem]">
      <div class="flex items-center justify-between p-6 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-purple-600">Tambah Mading</h3>
        <button type="button" data-close="add" class="text-gray-500"><i class="ri-close-line text-xl"></i></button>
      </div>
      <form id="formAddMading" method="post" action="#" enctype="multipart/form-data" class="p-6 space-y-4">
        <div>
          <label class="block text-sm mb-1">Judul</label>
          <input type="text" name="judul" required class="w-full px-3 py-2 border border-gray-200 rounded">
        </div>
        <div>
          <label class="block text-sm mb-1">Kategori</label>
          <select name="category" required class="w-full px-3 py-2 border border-gray-200 rounded">
            <option value="">Pilih Kategori</option>
            <?php foreach (($categories ?? []) as $cat): ?>
              <option value="<?= $cat ?>"><?= ucfirst($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Deskripsi</label>
          <textarea name="deskripsi" rows="4" class="w-full px-3 py-2 border border-gray-200 rounded"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm mb-1">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" class="w-full px-3 py-2 border border-gray-200 rounded">
          </div>
          <div>
            <label class="block text-sm mb-1">Tanggal Akhir</label>
            <input type="date" name="tgl_akhir" class="w-full px-3 py-2 border border-gray-200 rounded">
          </div>
        </div>
        <div>
          <label class="block text-sm mb-1">Status</label>
          <select name="status" required class="w-full px-3 py-2 border border-gray-200 rounded">
            <option value="pending">Pending</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Gambar (opsional)</label>
          <input type="file" name="file" accept="image/*" class="w-full border border-gray-200 rounded">
        </div>
        <div class="flex justify-end gap-3 pt-2">
          <button type="button" data-close="add" class="px-4 py-2 border border-gray-200 rounded">Batal</button>
          <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded">
            <span class="submit-text">Simpan</span>
            <span class="loading-text hidden">
              <i class="ri-loader-4-line animate-spin mr-2"></i>
              Menyimpan...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Modal Add Mading Handler
  (function() {
    function qs(sel) {
      return document.querySelector(sel);
    }

    // Modal add
    document.addEventListener('click', function(e) {
      var addBtn = e.target.closest('[data-action="add-mading"]');
      if (addBtn) {
        e.preventDefault();
        qs('#modalAddMading').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
      var closeAdd = e.target.closest('[data-close="add"]');
      if (closeAdd) {
        e.preventDefault();
        qs('#modalAddMading').classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
    });

    // Form submission
    var form = qs('#formAddMading');
    var submitBtn = qs('#formAddMading button[type="submit"]');
    var submitText = qs('#formAddMading .submit-text');
    var loadingText = qs('#formAddMading .loading-text');

    if (form && submitBtn) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loading state
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        loadingText.classList.remove('hidden');

        // Get form data
        var formData = new FormData(form);
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('csrf_test_name', csrfToken);

        // Submit via AJAX
        var client = (typeof window !== 'undefined' && window.api) ? window.api : axios;
        client.post('<?= site_url('Admin/MasterData/addMading') ?>', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(function(response) {
            if (response.data.success) {
              // Show success message
              if (typeof AdminSearchUtils !== 'undefined' && AdminSearchUtils.showNotification) {
                AdminSearchUtils.showNotification('Mading berhasil ditambahkan!', 'success');
              } else {
                alert('Mading berhasil ditambahkan!');
              }

              // Close modal
              qs('#modalAddMading').classList.add('hidden');
              document.body.style.overflow = 'auto';
              form.reset();

              // Refresh table data
              if (window.adminSearchFilter && typeof window.adminSearchFilter.performSearch === 'function') {
                window.adminSearchFilter.performSearch();
              } else {
                location.reload();
              }
            } else {
              throw new Error(response.data.message || 'Terjadi kesalahan');
            }
          })
          .catch(function(error) {
            console.error('Error:', error);
            var errorMessage = error.response?.data?.message || error.message || 'Terjadi kesalahan saat menambahkan mading';

            if (typeof AdminSearchUtils !== 'undefined' && AdminSearchUtils.showNotification) {
              AdminSearchUtils.showNotification(errorMessage, 'error');
            } else {
              alert(errorMessage);
            }
          })
          .finally(function() {
            // Reset button state
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            loadingText.classList.add('hidden');
          });
      });
    }
  })();
</script>
<?= $this->endSection() ?>