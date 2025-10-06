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
          <th class="px-4 py-3 text-left">Action</th>
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
<script src="<?= base_url('js/alerts.js') ?>"></script>
<script src="<?= base_url('js/admin-spa-router.js') ?>"></script>
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

    // Form submission is now handled by initializeAddMadingModal()
  })();

  // Mading Action Functions
  function updateMadingStatus(id, newStatus) {
    var statusText = newStatus === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
    var title = 'Konfirmasi Perubahan Status';
    var message = 'Apakah Anda yakin ingin ' + statusText + ' mading ini?';

    if (typeof window.showConfirm === 'function') {
      window.showConfirm(title, message, function() {
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var params = new URLSearchParams();
        params.append('mading_id', id);
        params.append('status', newStatus);
        params.append('csrf_test_name', csrf);

        if (window.api && window.api.post) {
          window.api.post('<?= site_url('Admin/MasterData/updateMadingStatus') ?>', params)
            .then(function(response) {
              var data = response && response.data ? response.data : {};
              if (data.csrf) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', data.csrf);
              }
              if (data.success) {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('success', data.message, 2500);
                }
                if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                  window.adminSearchFilter.performSearch();
                } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/mading') === 0) {
                  window.location.href = window.location.href;
                } else if (window.__adminSpaNavigate) {
                  window.__adminSpaNavigate(window.location.href, false);
                } else {
                  location.reload();
                }
              } else {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('error', data.message || 'Gagal mengubah status', 3500);
                } else {
                  alert('Error: ' + (data.message || 'Gagal mengubah status'));
                }
              }
            })
            .catch(function(error) {
              console.error('Error:', error);
              if (typeof window.showAlert === 'function') {
                window.showAlert('error', 'Terjadi kesalahan jaringan', 3500);
              } else {
                alert('Terjadi kesalahan jaringan');
              }
            });
        } else {
          fetch('<?= site_url('Admin/MasterData/updateMadingStatus') ?>', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf
              },
              body: params.toString()
            })
            .then(function(response) {
              return response.json();
            })
            .then(function(data) {
              if (data.success) {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('success', data.message, 2500);
                }
                if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                  window.adminSearchFilter.performSearch();
                } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/mading') === 0) {
                  window.location.href = window.location.href;
                } else if (window.__adminSpaNavigate) {
                  window.__adminSpaNavigate(window.location.href, false);
                } else {
                  location.reload();
                }
              } else {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('error', data.message || 'Gagal mengubah status', 3500);
                } else {
                  alert('Error: ' + (data.message || 'Gagal mengubah status'));
                }
              }
            })
            .catch(function(error) {
              console.error('Error:', error);
              if (typeof window.showAlert === 'function') {
                window.showAlert('error', 'Terjadi kesalahan jaringan', 3500);
              } else {
                alert('Terjadi kesalahan jaringan');
              }
            });
        }
      }, function() {});
    } else {
      if (confirm('Are you sure you want to change this mading status?')) {
        updateMadingStatus(id, newStatus);
      }
    }
  }

  function deleteMading(id) {
    var title = 'Hapus Mading';
    var message = 'Anda yakin ingin menghapus mading ini? Tindakan ini tidak dapat dibatalkan.';
    if (typeof window.showConfirm === 'function') {
      window.showConfirm(title, message, function() {
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var params = new URLSearchParams();
        params.append('mading_id', id);
        params.append('csrf_test_name', csrf);

        if (window.api && window.api.post) {
          window.api.post('<?= site_url('Admin/MasterData/deleteMading') ?>', params)
            .then(function(response) {
              var data = response && response.data ? response.data : {};
              if (data.csrf) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', data.csrf);
              }
              if (data.success) {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('success', 'Mading berhasil dihapus!', 2500);
                }
                if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                  window.adminSearchFilter.performSearch();
                } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/mading') === 0) {
                  window.location.href = window.location.href;
                } else if (window.__adminSpaNavigate) {
                  window.__adminSpaNavigate(window.location.href, false);
                } else {
                  location.reload();
                }
              } else {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('error', data.message || 'Gagal menghapus mading', 3500);
                } else {
                  alert('Error: ' + (data.message || 'Gagal menghapus mading'));
                }
              }
            })
            .catch(function(error) {
              console.error('Error:', error);
              if (typeof window.showAlert === 'function') {
                window.showAlert('error', 'Terjadi kesalahan jaringan', 3500);
              } else {
                alert('Terjadi kesalahan jaringan');
              }
            });
        } else {
          fetch('<?= site_url('Admin/MasterData/deleteMading') ?>', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf
              },
              body: params.toString()
            })
            .then(function(response) {
              return response.json();
            })
            .then(function(data) {
              if (data.success) {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('success', 'Mading berhasil dihapus!', 2500);
                }
                if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                  window.adminSearchFilter.performSearch();
                } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/mading') === 0) {
                  window.location.href = window.location.href;
                } else if (window.__adminSpaNavigate) {
                  window.__adminSpaNavigate(window.location.href, false);
                } else {
                  location.reload();
                }
              } else {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('error', data.message || 'Gagal menghapus mading', 3500);
                } else {
                  alert('Error: ' + (data.message || 'Gagal menghapus mading'));
                }
              }
            })
            .catch(function(error) {
              console.error('Error:', error);
              if (typeof window.showAlert === 'function') {
                window.showAlert('error', 'Terjadi kesalahan jaringan', 3500);
              } else {
                alert('Terjadi kesalahan jaringan');
              }
            });
        }
      }, function() {});
    } else {
      if (confirm('Are you sure you want to delete this mading? This action cannot be undone.')) {
        deleteMading(id);
      }
    }
  }

  // Edit Mading Modal
  document.addEventListener('click', function(e) {
    if (e.target.closest('[data-action="edit-mading"]')) {
      const btn = e.target.closest('[data-action="edit-mading"]');
      const modal = document.getElementById('modalEditMading');

      // Fill form with data
      const editMadingId = document.getElementById('edit_mading_id');
      const editJudul = document.getElementById('edit_judul');
      const editCategory = document.getElementById('edit_category');
      const editDeskripsi = document.getElementById('edit_deskripsi');
      const editTglMulai = document.getElementById('edit_tgl_mulai');
      const editTglAkhir = document.getElementById('edit_tgl_akhir');
      const editStatus = document.getElementById('edit_status');

      if (editMadingId) editMadingId.value = btn.dataset.madingId;
      if (editJudul) editJudul.value = btn.dataset.judul;
      if (editCategory) editCategory.value = btn.dataset.category;
      if (editDeskripsi) editDeskripsi.value = btn.dataset.deskripsi;
      if (editTglMulai) editTglMulai.value = btn.dataset.tglMulai;
      if (editTglAkhir) editTglAkhir.value = btn.dataset.tglAkhir;
      if (editStatus) editStatus.value = btn.dataset.status;

      if (modal) {
        modal.classList.remove('hidden');
      }
    }
  });

  // Close Edit Modal
  document.addEventListener('click', function(e) {
    if (e.target.closest('[data-close="edit"]')) {
      const modalEditMading = document.getElementById('modalEditMading');
      if (modalEditMading) {
        modalEditMading.classList.add('hidden');
      }
    }
  });

  // Edit Mading Form Submit is now handled by initializeEditMadingModal()

  // Initialize Edit Mading Modal for SPA compatibility
  window.initializeEditMadingModal = function() {
    try {
      var modal = document.getElementById('modalEditMading');
      var form = document.getElementById('formEditMading');
      var closeBtn = document.querySelector('[data-close="edit"]');
      var cancelBtn = document.querySelector('#formEditMading button[data-close="edit"]');
      var submitBtn = document.querySelector('#formEditMading button[type="submit"]');

      if (!modal || !form || !submitBtn) return;

      function openEditModal(data) {
        document.getElementById('edit_mading_id').value = data.id;
        document.getElementById('edit_judul').value = data.judul || '';
        document.getElementById('edit_category').value = data.category || '';
        document.getElementById('edit_deskripsi').value = data.deskripsi || '';
        document.getElementById('edit_tgl_mulai').value = data.tglMulai || '';
        document.getElementById('edit_tgl_akhir').value = data.tglAkhir || '';
        document.getElementById('edit_status').value = data.status || 'pending';

        // Handle current image display
        var currentImageDiv = document.getElementById('edit_current_image');
        var imagePreview = document.getElementById('edit_image_preview');
        if (data.file && data.file.trim() !== '') {
          imagePreview.src = '<?= base_url() ?>' + data.file;
          currentImageDiv.classList.remove('hidden');
        } else {
          currentImageDiv.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }

      function closeEditModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        form.reset();

        // Reset image display
        var currentImageDiv = document.getElementById('edit_current_image');
        var imagePreview = document.getElementById('edit_image_preview');
        currentImageDiv.classList.add('hidden');
        imagePreview.src = '';
      }

      // Delegasi click untuk tombol edit
      document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-action="edit-mading"]');
        if (!btn) return;
        e.preventDefault();
        var data = {
          id: btn.getAttribute('data-mading-id'),
          judul: btn.getAttribute('data-judul'),
          category: btn.getAttribute('data-category'),
          deskripsi: btn.getAttribute('data-deskripsi'),
          tglMulai: btn.getAttribute('data-tgl-mulai'),
          tglAkhir: btn.getAttribute('data-tgl-akhir'),
          status: btn.getAttribute('data-status'),
          file: btn.getAttribute('data-file')
        };
        openEditModal(data);
      });

      if (closeBtn) closeBtn.addEventListener('click', closeEditModal);
      if (cancelBtn) cancelBtn.addEventListener('click', closeEditModal);

      // Form submission handler
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        var st = submitBtn.querySelector('.submit-text');
        var lt = submitBtn.querySelector('.loading-text');
        if (st) st.classList.add('hidden');
        if (lt) lt.classList.remove('hidden');

        var fd = new FormData(form);
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fd.append('csrf_test_name', csrf);

        (window.api && window.api.post ? window.api.post('<?= site_url('Admin/MasterData/updateMading') ?>', fd, {
            headers: {
              'Content-Type': 'multipart/form-data',
              'X-Requested-With': 'XMLHttpRequest'
            }
          }) :
          fetch('<?= site_url('Admin/MasterData/updateMading') ?>', {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': csrf
            },
            body: fd
          }).then(function(r) {
            return r.json();
          }))
        .then(function(res) {
            var data = res && res.data ? res.data : res;
            if (data && data.csrf) {
              var m = document.querySelector('meta[name="csrf-token"]');
              if (m) m.setAttribute('content', data.csrf);
            }
            if (data && data.success) {
              if (typeof window.showAlert === 'function') {
                window.showAlert('success', data.message || 'Mading berhasil diperbarui', 2500);
              }
              closeEditModal();
              if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                window.adminSearchFilter.performSearch();
              } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/mading') === 0) {
                window.location.href = window.location.href;
              } else if (window.__adminSpaNavigate) {
                window.__adminSpaNavigate(window.location.href, false);
              } else {
                location.reload();
              }
            } else {
              if (typeof window.showAlert === 'function') {
                window.showAlert('error', data.message || 'Gagal memperbarui mading', 3500);
              } else {
                alert('Error: ' + (data.message || 'Gagal memperbarui mading'));
              }
            }
          })
          .catch(function(err) {
            console.error(err);
            if (typeof window.showAlert === 'function') {
              window.showAlert('error', 'Terjadi kesalahan jaringan', 3500);
            } else {
              alert('Terjadi kesalahan jaringan');
            }
          })
          .finally(function() {
            submitBtn.disabled = false;
            if (st) st.classList.remove('hidden');
            if (lt) lt.classList.add('hidden');
          });
      });
    } catch (e) {
      console.error('init edit mading modal error', e);
    }
  };

  // Initialize Add Mading Modal for SPA compatibility
  window.initializeAddMadingModal = function() {
    try {
      var modal = document.getElementById('modalAddMading');
      var form = document.getElementById('formAddMading');
      var closeBtn = document.querySelector('[data-close="add"]');
      var cancelBtn = document.querySelector('#formAddMading button[data-close="add"]');
      var submitBtn = document.querySelector('#formAddMading button[type="submit"]');

      if (!modal || !form || !submitBtn) return;

      function openAddModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }

      function closeAddModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        form.reset();
      }

      // Delegasi click untuk tombol add
      document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-action="add-mading"]');
        if (!btn) return;
        e.preventDefault();
        openAddModal();
      });

      if (closeBtn) closeBtn.addEventListener('click', closeAddModal);
      if (cancelBtn) cancelBtn.addEventListener('click', closeAddModal);

      // Form submission handler
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        var st = submitBtn.querySelector('.submit-text');
        var lt = submitBtn.querySelector('.loading-text');
        if (st) st.classList.add('hidden');
        if (lt) lt.classList.remove('hidden');

        var fd = new FormData(form);
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fd.append('csrf_test_name', csrf);

        (window.api && window.api.post ? window.api.post('<?= site_url('Admin/MasterData/addMading') ?>', fd, {
            headers: {
              'Content-Type': 'multipart/form-data',
              'X-Requested-With': 'XMLHttpRequest'
            }
          }) :
          fetch('<?= site_url('Admin/MasterData/addMading') ?>', {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': csrf
            },
            body: fd
          }).then(function(r) {
            return r.json();
          }))
        .then(function(res) {
            var data = res && res.data ? res.data : res;
            if (data && data.csrf) {
              var m = document.querySelector('meta[name="csrf-token"]');
              if (m) m.setAttribute('content', data.csrf);
            }
            if (data && data.success) {
              if (typeof window.showAlert === 'function') {
                window.showAlert('success', data.message || 'Mading berhasil ditambahkan', 2500);
              }
              closeAddModal();
              if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                window.adminSearchFilter.performSearch();
              } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/mading') === 0) {
                window.location.href = window.location.href;
              } else if (window.__adminSpaNavigate) {
                window.__adminSpaNavigate(window.location.href, false);
              } else {
                location.reload();
              }
            } else {
              if (typeof window.showAlert === 'function') {
                window.showAlert('error', data.message || 'Gagal menambahkan mading', 3500);
              } else {
                alert('Error: ' + (data.message || 'Gagal menambahkan mading'));
              }
            }
          })
          .catch(function(err) {
            console.error(err);
            if (typeof window.showAlert === 'function') {
              window.showAlert('error', 'Terjadi kesalahan jaringan', 3500);
            } else {
              alert('Terjadi kesalahan jaringan');
            }
          })
          .finally(function() {
            submitBtn.disabled = false;
            if (st) st.classList.remove('hidden');
            if (lt) lt.classList.add('hidden');
          });
      });
    } catch (e) {
      console.error('init add mading modal error', e);
    }
  };

  // Initialize modals on page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      window.initializeEditMadingModal();
      window.initializeAddMadingModal();
    });
  } else {
    window.initializeEditMadingModal();
    window.initializeAddMadingModal();
  }
</script>

<!-- Modal Edit Mading -->
<div id="modalEditMading" class="fixed overflow-y-auto inset-0 bg-black/40 hidden z-50">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-[38rem]">
      <div class="flex items-center justify-between p-6 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-purple-600">Edit Mading</h3>
        <button type="button" data-close="edit" class="text-gray-500"><i class="ri-close-line text-xl"></i></button>
      </div>
      <form id="formEditMading" method="post" action="#" enctype="multipart/form-data" class="p-6 space-y-4">
        <input type="hidden" name="mading_id" id="edit_mading_id">
        <div>
          <label class="block text-sm mb-1">Judul</label>
          <input type="text" name="judul" id="edit_judul" required class="w-full px-3 py-2 border border-gray-200 rounded">
        </div>
        <div>
          <label class="block text-sm mb-1">Kategori</label>
          <select name="category" id="edit_category" required class="w-full px-3 py-2 border border-gray-200 rounded">
            <option value="">Pilih Kategori</option>
            <?php foreach (($categories ?? []) as $cat): ?>
              <option value="<?= $cat ?>"><?= ucfirst($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Deskripsi</label>
          <textarea name="deskripsi" id="edit_deskripsi" rows="4" class="w-full px-3 py-2 border border-gray-200 rounded"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm mb-1">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" id="edit_tgl_mulai" class="w-full px-3 py-2 border border-gray-200 rounded">
          </div>
          <div>
            <label class="block text-sm mb-1">Tanggal Akhir</label>
            <input type="date" name="tgl_akhir" id="edit_tgl_akhir" class="w-full px-3 py-2 border border-gray-200 rounded">
          </div>
        </div>
        <div>
          <label class="block text-sm mb-1">Status</label>
          <select name="status" id="edit_status" required class="w-full px-3 py-2 border border-gray-200 rounded">
            <option value="pending">Pending</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Gambar (opsional)</label>
          <div id="edit_current_image" class="mb-2 hidden">
            <p class="text-xs text-gray-600 mb-1">Gambar saat ini:</p>
            <img id="edit_image_preview" src="" alt="Current image" class="w-32 h-24 object-cover rounded border">
          </div>
          <input type="file" name="file" accept="image/*" class="w-full border border-gray-200 rounded">
          <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah gambar</p>
        </div>
        <div class="flex justify-end gap-3 pt-2">
          <button type="button" data-close="edit" class="px-4 py-2 border border-gray-200 rounded">Batal</button>
          <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded">
            <span class="submit-text">Update</span>
            <span class="loading-text hidden">
              <i class="ri-loader-4-line animate-spin mr-2"></i>
              Mengupdate...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>