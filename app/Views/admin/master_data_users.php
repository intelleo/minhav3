<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="admin-users-management mt-[-5rem]">
  <!-- Header Section -->
  <!-- Statistics Cards -->
  <?= view('admin/partials/users_stats', ['stats' => $stats]) ?>


  <!-- Search & Filter -->
  <?= view('admin/partials/users_filters', [
    'filters' => $filters,
    'jurusanList' => $jurusanList
  ]) ?>

  <!-- Users Table -->
  <?= view('admin/partials/users_table', [
    'users' => $users,
    'pagination' => $pagination,
    'filters' => $filters
  ]) ?>

</div>

<script>
  function updateUserStatus(userId, newStatus) {
    var title = 'Ubah Status User';
    var message = 'Anda yakin ingin mengubah status user ini?';
    if (typeof window.showConfirm === 'function') {
      window.showConfirm(title, message, function() {
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var params = new URLSearchParams();
        params.append('user_id', userId);
        params.append('status', newStatus);
        params.append('csrf_test_name', csrf);

        if (window.api && window.api.post) {
          window.api.post('<?= site_url('Admin/MasterData/updateUserStatus') ?>', params)
            .then(function(response) {
              var data = response && response.data ? response.data : {};
              if (data.csrf) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', data.csrf);
              }
              if (data.success) {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('success', 'Status berhasil diperbarui!', 2500);
                }
                if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                  window.adminSearchFilter.performSearch();
                } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/users') === 0) {
                  window.location.href = window.location.href;
                } else if (window.__adminSpaNavigate) {
                  window.__adminSpaNavigate(window.location.href, false);
                } else {
                  location.reload();
                }
              } else {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('error', data.message || 'Gagal memperbarui status', 3500);
                } else {
                  alert('Error: ' + (data.message || 'Gagal memperbarui status'));
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
          fetch('<?= site_url('Admin/MasterData/updateUserStatus') ?>', {
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
                  window.showAlert('success', 'Status berhasil diperbarui!', 2500);
                }
                if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                  window.adminSearchFilter.performSearch();
                } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/users') === 0) {
                  window.location.href = window.location.href;
                } else if (window.__adminSpaNavigate) {
                  window.__adminSpaNavigate(window.location.href, false);
                } else {
                  location.reload();
                }
              } else {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('error', data.message || 'Gagal memperbarui status', 3500);
                } else {
                  alert('Error: ' + (data.message || 'Gagal memperbarui status'));
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
      if (confirm('Are you sure you want to change this user status?')) {
        updateUserStatus(userId, newStatus);
      }
    }
  }

  function deleteUser(userId) {
    var title = 'Hapus User';
    var message = 'Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.';
    if (typeof window.showConfirm === 'function') {
      window.showConfirm(title, message, function() {
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var params = new URLSearchParams();
        params.append('user_id', userId);
        params.append('csrf_test_name', csrf);

        if (window.api && window.api.post) {
          window.api.post('<?= site_url('Admin/MasterData/deleteUser') ?>', params)
            .then(function(response) {
              var data = response && response.data ? response.data : {};
              if (data.csrf) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', data.csrf);
              }
              if (data.success) {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('success', 'User berhasil dihapus!', 2500);
                }
                if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                  window.adminSearchFilter.performSearch();
                } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/users') === 0) {
                  window.location.href = window.location.href;
                } else if (window.__adminSpaNavigate) {
                  window.__adminSpaNavigate(window.location.href, false);
                } else {
                  location.reload();
                }
              } else {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('error', data.message || 'Gagal menghapus user', 3500);
                } else {
                  alert('Error: ' + (data.message || 'Gagal menghapus user'));
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
          fetch('<?= site_url('Admin/MasterData/deleteUser') ?>', {
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
                  window.showAlert('success', 'User berhasil dihapus!', 2500);
                }
                if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                  window.adminSearchFilter.performSearch();
                } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/users') === 0) {
                  window.location.href = window.location.href;
                } else if (window.__adminSpaNavigate) {
                  window.__adminSpaNavigate(window.location.href, false);
                } else {
                  location.reload();
                }
              } else {
                if (typeof window.showAlert === 'function') {
                  window.showAlert('error', data.message || 'Gagal menghapus user', 3500);
                } else {
                  alert('Error: ' + (data.message || 'Gagal menghapus user'));
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
      if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        deleteUser(userId);
      }
    }
  }
</script>

<!-- Load Axios and Admin Search Filter -->
<script src="<?= base_url('vendor/axios.min.js') ?>"></script>
<script src="<?= base_url('js/admin-search-filter.js') ?>"></script>
<script>
  // Inisialisasi eksplisit agar search berfungsi sejak load pertama (SSR/SPA)
  (function() {
    try {
      // Matikan auto-init bawaan untuk mencegah duplikasi di SPA
      window.__DISABLE_ADMIN_SEARCH_AUTOINIT = true;

      if (window.AdminSearchFilter) {
        window.adminSearchFilter = null;
        window.adminSearchFilter = new AdminSearchFilter({
          baseUrl: '<?= site_url('Admin/MasterData/users') ?>',
          searchInput: '.search-input',
          statusSelect: '.status-select',
          jurusanSelect: '.jurusan-select',
          resultsContainer: '.results-container',
          paginationContainer: '.pagination-container',
          debounceDelay: 400,
          autoLoad: false // gunakan data SSR; AJAX hanya untuk interaksi berikutnya
        });
      }

      // Pastikan tidak ada state loading tertinggal dari SSR
      var rc = document.querySelector('.results-container');
      if (rc) rc.classList.remove('loading');
    } catch (e) {
      console.error(e);
    }
  })();
</script>
<script>
  // Pastikan baseUrl untuk AJAX mengarah ke endpoint users (hindari bergantung pada pathname/SPA)
  document.addEventListener('DOMContentLoaded', function() {
    try {
      // Hentikan instance lama bila ada lalu inisialisasi dengan baseUrl eksplisit
      if (window.AdminSearchFilter) {
        window.adminSearchFilter = null;
        window.adminSearchFilter = new AdminSearchFilter({
          baseUrl: '<?= site_url('Admin/MasterData/users') ?>',
          searchInput: '.search-input',
          statusSelect: '.status-select',
          jurusanSelect: '.jurusan-select',
          resultsContainer: '.results-container',
          paginationContainer: '.pagination-container',
          debounceDelay: 400,
          autoLoad: false // jangan load di awal, gunakan data server-side
        });
      }

      // Pastikan tidak ada sisa state loading saat pertama render server-side
      var rc = document.querySelector('.results-container');
      if (rc) rc.classList.remove('loading');
    } catch (e) {
      console.error(e);
    }
  });
</script>

<style>
  .loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
  }

  .loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed overflow-x-auto inset-0 bg-[#00000049] bg-opacity-10 hidden z-50">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-[35rem]">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-blue-50">
        <h3 class="text-lg font-semibold text-blue-600">Tambah User Baru</h3>
        <button type="button" id="closeAddUserModal" class="text-gray-400 hover:text-gray-600">
          <i class="ri-close-line text-xl"></i>
        </button>
      </div>

      <!-- Modal Body -->
      <form id="addUserForm" class="p-6" method="post" action="#" onsubmit="return false;">
        <div class="space-y-4">
          <!-- Nama Lengkap -->
          <div>
            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>

          <!-- NPM -->
          <div>
            <label for="npm" class="block text-sm font-medium text-gray-700 mb-1">NPM</label>
            <input type="text" id="npm" name="npm" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>

          <!-- Jurusan -->
          <div>
            <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
            <select id="jurusan" name="jurusan" required
              class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Pilih Jurusan</option>
              <?php if (isset($jurusanOptions)): ?>
                <?php foreach ($jurusanOptions as $value => $label): ?>
                  <option value="<?= $value ?>"><?= $label ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" id="password" name="password" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>

          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="pending">Pending</option>
              <option value="aktif">Aktif</option>
              <option value="nonaktif">Nonaktif</option>
            </select>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex gap-4 mt-6">
          <button type="button" id="cancelAddUser" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
            <span class="text-gray-700">Batal</span>
          </button>
          <button type="button" id="submitAddUser" class="px-6 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <span class="submit-text text-white">Tambah User</span>
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

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed overflow-x-auto inset-0 bg-[#00000049] bg-opacity-10 hidden z-50">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-[35rem]">
      <div class="flex items-center justify-between p-6 border-b border-blue-50">
        <h3 class="text-lg font-semibold text-blue-600">Edit User</h3>
        <button type="button" id="closeEditUserModal" class="text-gray-400 hover:text-gray-600">
          <i class="ri-close-line text-xl"></i>
        </button>
      </div>
      <form id="editUserForm" class="p-6" method="post" action="#" onsubmit="return false;">
        <input type="hidden" id="edit_user_id" name="user_id" />
        <div class="space-y-4">
          <div>
            <label for="edit_nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label for="edit_npm" class="block text-sm font-medium text-gray-700 mb-1">NPM</label>
            <input type="text" id="edit_npm" name="npm" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label for="edit_jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
            <select id="edit_jurusan" name="jurusan" required class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Pilih Jurusan</option>
              <?php if (isset($jurusanOptions)): ?>
                <?php foreach ($jurusanOptions as $value => $label): ?>
                  <option value="<?= $value ?>"><?= $label ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <div>
            <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="edit_status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="pending">Pending</option>
              <option value="aktif">Aktif</option>
              <option value="nonaktif">Nonaktif</option>
            </select>
          </div>
          <div>
            <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-1">Password (opsional)</label>
            <input type="password" id="edit_password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Biarkan kosong jika tidak diubah">
          </div>
        </div>
        <div class="flex gap-4 mt-6">
          <button type="button" id="cancelEditUser" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
            <span class="text-gray-700">Batal</span>
          </button>
          <button type="button" id="submitEditUser" class="px-6 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <span class="submit-text text-white">Simpan Perubahan</span>
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
  // Global function untuk initialize modal - bisa dipanggil dari SPA router
  window.initializeAddUserModal = function() {
    console.log('Initializing Add User modal...');

    try {
      const addUserModal = document.getElementById('addUserModal');
      const addUserForm = document.getElementById('addUserForm');
      const closeModalBtn = document.getElementById('closeAddUserModal');
      const cancelBtn = document.getElementById('cancelAddUser');
      const submitBtn = document.getElementById('submitAddUser');

      if (!addUserModal || !addUserForm || !submitBtn) {
        console.log('Modal elements not found, skipping initialization');
        return;
      }

      const submitText = submitBtn.querySelector('.submit-text');
      const loadingText = submitBtn.querySelector('.loading-text');

      console.log('Modal elements found:', {
        addUserModal: !!addUserModal,
        addUserForm: !!addUserForm,
        closeModalBtn: !!closeModalBtn,
        cancelBtn: !!cancelBtn,
        submitBtn: !!submitBtn
      });

      // Open modal when Add User button is clicked - using event delegation
      function openModal() {
        console.log('Opening Add User modal...');

        // Debug modal element
        const currentModal = document.getElementById('addUserModal');
        console.log('Current modal element:', currentModal);
        console.log('Modal classes before:', currentModal ? currentModal.className : 'Modal not found');

        if (currentModal) {
          currentModal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
          console.log('Modal classes after:', currentModal.className);
          console.log('Modal display style:', window.getComputedStyle(currentModal).display);
          console.log('Modal visibility:', window.getComputedStyle(currentModal).visibility);
          console.log('Modal opacity:', window.getComputedStyle(currentModal).opacity);
        } else {
          console.error('Modal element not found!');
        }
      }

      // Global function to open modal (for debugging)
      window.openAddUserModal = function() {
        console.log('Global function called to open modal');
        openModal();
      };

      // Use event delegation for better reliability - with stopPropagation
      document.addEventListener('click', function(e) {
        console.log('Click detected:', e.target);
        if (e.target.closest('[data-action="add-user"]')) {
          console.log('Add User button clicked!');
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          openModal();
        }
      });

      // Additional event listener for better reliability
      document.addEventListener('click', function(e) {
        if (e.target.matches('[data-action="add-user"]') || e.target.closest('[data-action="add-user"]')) {
          console.log('Add User button clicked (additional listener)!');
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          openModal();
        }
      });

      // Fallback: Direct button event listener - with stopPropagation
      const addUserButton = document.querySelector('[data-action="add-user"]');
      if (addUserButton) {
        addUserButton.addEventListener('click', function(e) {
          console.log('Add User button clicked (direct listener)!');
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          openModal();
        });
      }

      // Close modal functions
      function closeModal() {
        addUserModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        addUserForm.reset();
        resetSubmitButton();
      }

      // Re-initialize modal after page refresh
      function reinitializeModal() {
        console.log('Re-initializing modal...');

        // Check if modal still exists
        const modalExists = document.getElementById('addUserModal');
        console.log('Modal exists after refresh:', !!modalExists);

        const newAddUserButton = document.querySelector('[data-action="add-user"]');
        console.log('Add User button exists after refresh:', !!newAddUserButton);

        if (newAddUserButton && !newAddUserButton.hasAttribute('data-initialized')) {
          // Remove old event listeners by cloning the button
          const newButton = newAddUserButton.cloneNode(true);
          newAddUserButton.parentNode.replaceChild(newButton, newAddUserButton);

          newButton.addEventListener('click', function(e) {
            console.log('Add User button clicked (re-initialized)!');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            openModal();
          });
          newButton.setAttribute('data-initialized', 'true');
        }

        // Re-initialize submit button
        const newSubmitButton = document.getElementById('submitAddUser');
        const newForm = document.getElementById('addUserForm');
        console.log('Submit button exists after refresh:', !!newSubmitButton);
        console.log('Form exists after refresh:', !!newForm);

        if (newSubmitButton && newForm && !newSubmitButton.hasAttribute('data-submit-initialized')) {
          console.log('Re-initializing submit button...');

          // Remove old event listeners by cloning the button
          const newButton = newSubmitButton.cloneNode(true);
          newSubmitButton.parentNode.replaceChild(newButton, newSubmitButton);

          // Add new event listener
          newButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Submit button clicked (re-initialized)!');

            // Get form data
            const formData = new FormData(newForm);
            const data = Object.fromEntries(formData.entries());

            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('CSRF Token:', csrfToken);
            console.log('Form Data:', data);

            // Add CSRF token to data
            data['csrf_test_name'] = csrfToken;

            // Create URLSearchParams
            const params = new URLSearchParams();
            Object.keys(data).forEach(key => {
              params.append(key, data[key]);
            });

            console.log('URLSearchParams:', params.toString());

            // Show loading state
            const submitText = newButton.querySelector('.submit-text');
            const loadingText = newButton.querySelector('.loading-text');
            newButton.disabled = true;
            if (submitText) submitText.classList.add('hidden');
            if (loadingText) loadingText.classList.remove('hidden');

            // Submit via AJAX
            axios.post('<?= site_url('Admin/MasterData/addUserTest') ?>', params.toString(), {
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': csrfToken
                }
              })
              .then(function(response) {
                // Update CSRF token if provided
                if (response.data && response.data.csrf) {
                  const meta = document.querySelector('meta[name="csrf-token"]');
                  if (meta) meta.setAttribute('content', response.data.csrf);
                  console.log('CSRF token updated:', response.data.csrf);
                }

                if (response.data.success) {
                  // Show success message
                  if (typeof AdminSearchUtils !== 'undefined' && AdminSearchUtils.showNotification) {
                    AdminSearchUtils.showNotification('User berhasil ditambahkan!', 'success');
                  } else {
                    alert('User berhasil ditambahkan!');
                  }

                  // Close modal
                  const modal = document.getElementById('addUserModal');
                  if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    newForm.reset();
                  }

                  // Refresh table data
                  console.log('Refreshing table after successful add...');
                  if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/users') === 0) {
                    window.location.href = window.location.href;
                  } else if (window.__adminSpaNavigate) {
                    window.__adminSpaNavigate(window.location.href, false);
                  } else {
                    location.reload();
                  }
                } else {
                  throw new Error(response.data.message || 'Terjadi kesalahan');
                }
              })
              .catch(function(error) {
                console.error('Error:', error);
                const errorMessage = error.response?.data?.message || error.message || 'Terjadi kesalahan saat menambahkan user';

                if (typeof AdminSearchUtils !== 'undefined' && AdminSearchUtils.showNotification) {
                  AdminSearchUtils.showNotification(errorMessage, 'error');
                } else {
                  alert(errorMessage);
                }
              })
              .finally(function() {
                // Reset button state
                newButton.disabled = false;
                if (submitText) submitText.classList.remove('hidden');
                if (loadingText) loadingText.classList.add('hidden');
              });
          });

          newButton.setAttribute('data-submit-initialized', 'true');
        }
      }

      // Call re-initialization after a short delay to ensure DOM is ready
      setTimeout(reinitializeModal, 100);

      // Also re-initialize after SPA navigation
      if (window.__adminSpaNavigate) {
        const originalNavigate = window.__adminSpaNavigate;
        window.__adminSpaNavigate = function(url, replace) {
          console.log('SPA navigation detected, re-initializing modal...');
          const result = originalNavigate(url, replace);
          setTimeout(reinitializeModal, 500); // Wait longer for SPA to complete
          return result;
        };
      }

      // Additional debugging for modal issues
      console.log('Modal initialization complete. Testing modal access...');
      setTimeout(function() {
        const testModal = document.getElementById('addUserModal');
        const testButton = document.querySelector('[data-action="add-user"]');
        console.log('Modal test after 1 second:', !!testModal);
        console.log('Button test after 1 second:', !!testButton);

        if (testModal && testButton) {
          console.log('Modal and button are available for testing');
          // Test if modal can be opened
          window.testModal = function() {
            console.log('Testing modal open...');
            openModal();
          };
        }
      }, 1000);

      // Debug: Monitor for page reloads
      window.addEventListener('beforeunload', function(e) {
        console.log('Page is about to reload!');
      });

      // Debug: Monitor for form submissions
      document.addEventListener('submit', function(e) {
        console.log('Form submission detected:', e.target);
        if (e.target.id === 'addUserForm') {
          console.log('Add User form submission prevented');
        }
      });

      // Reset submit button state
      function resetSubmitButton() {
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        loadingText.classList.add('hidden');
      }

      // Event listeners for closing modal
      closeModalBtn.addEventListener('click', closeModal);
      cancelBtn.addEventListener('click', closeModal);

      // Close modal when clicking outside
      addUserModal.addEventListener('click', function(e) {
        if (e.target === addUserModal) {
          closeModal();
        }
      });

      // Close modal with Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !addUserModal.classList.contains('hidden')) {
          closeModal();
        }
      });

      // Form submission - prevent default form submission
      addUserForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Form submit prevented');
        return false;
      });

      // Button submit click handler
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Submit button clicked');

        // Show loading state
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        loadingText.classList.remove('hidden');

        // Get form data
        const formData = new FormData(addUserForm);
        const data = Object.fromEntries(formData.entries());

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('CSRF Token:', csrfToken);
        console.log('Form Data:', data);

        // Add CSRF token to data
        data['csrf_test_name'] = csrfToken;

        // Create URLSearchParams
        const params = new URLSearchParams();
        Object.keys(data).forEach(key => {
          params.append(key, data[key]);
        });

        console.log('URLSearchParams:', params.toString());

        // Submit via AJAX using axios with URLSearchParams
        axios.post('<?= site_url('Admin/MasterData/addUserTest') ?>', params.toString(), {
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': csrfToken
            }
          })
          .then(function(response) {
            // Update CSRF token if provided
            if (response.data && response.data.csrf) {
              const meta = document.querySelector('meta[name="csrf-token"]');
              if (meta) meta.setAttribute('content', response.data.csrf);
              console.log('CSRF token updated:', response.data.csrf);
            }

            if (response.data.success) {
              // Show success message
              if (typeof AdminSearchUtils !== 'undefined' && AdminSearchUtils.showNotification) {
                AdminSearchUtils.showNotification('User berhasil ditambahkan!', 'success');
              } else {
                alert('User berhasil ditambahkan!');
              }

              // Close modal
              closeModal();

              // Refresh table data - force reload
              console.log('Refreshing table after successful add...');
              if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/users') === 0) {
                window.location.href = window.location.href;
              } else if (window.__adminSpaNavigate) {
                window.__adminSpaNavigate(window.location.href, false);
              } else {
                location.reload();
              }
            } else {
              throw new Error(response.data.message || 'Terjadi kesalahan');
            }
          })
          .catch(function(error) {
            console.error('Error:', error);

            // Update CSRF token if provided in error response
            if (error.response && error.response.data && error.response.data.csrf) {
              const meta = document.querySelector('meta[name="csrf-token"]');
              if (meta) meta.setAttribute('content', error.response.data.csrf);
              console.log('CSRF token updated from error:', error.response.data.csrf);
            }

            const errorMessage = error.response?.data?.message || error.message || 'Terjadi kesalahan saat menambahkan user';

            if (typeof AdminSearchUtils !== 'undefined' && AdminSearchUtils.showNotification) {
              AdminSearchUtils.showNotification(errorMessage, 'error');
            } else {
              alert(errorMessage);
            }
          })
          .finally(function() {
            resetSubmitButton();
          });
      });
    } catch (error) {
      console.error('Error initializing Add User modal:', error);
    }
  };

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initializeAddUserModal);
  } else {
    window.initializeAddUserModal();
  }

  // Pagination functionality - Global function untuk SPA
  window.initializePagination = function() {
    console.log('Initializing pagination...');

    // Remove existing event listeners to prevent duplication
    if (window.paginationClickHandler) {
      document.removeEventListener('click', window.paginationClickHandler);
    }

    // Create new event handler
    window.paginationClickHandler = function(e) {
      if (e.target.closest('.pagination-link')) {
        e.preventDefault();
        e.stopPropagation();

        const link = e.target.closest('.pagination-link');
        const page = link.getAttribute('data-page');

        if (page) {
          console.log('Pagination clicked, page:', page);
          loadPage(page);
        }
      }
    };

    // Add event delegation for pagination links
    document.addEventListener('click', window.paginationClickHandler);

    // Sorting click handler
    if (window.sortClickHandler) {
      document.removeEventListener('click', window.sortClickHandler);
    }
    window.sortClickHandler = function(e) {
      var btn = e.target.closest('.sort-btn');
      if (!btn) return;
      e.preventDefault();
      if (window.adminSearchFilter && typeof window.adminSearchFilter.setSort === 'function') {
        window.adminSearchFilter.setSort(btn.getAttribute('data-sort-by'));
        // Update indikator panah pada header
        try {
          var active = btn.getAttribute('data-sort-by');
          var dir = window.adminSearchFilter.currentFilters.sortDir || '';
          document.querySelectorAll('.sort-indicator').forEach(function(i) {
            i.textContent = '';
          });
          var target = document.querySelector('.sort-indicator[data-for="' + active + '"]');
          if (target) {
            target.textContent = dir === 'desc' ? '▼' : '▲';
          }
        } catch (_) {}
      }
    };
    document.addEventListener('click', window.sortClickHandler);
  };

  function loadPage(page) {
    console.log('Loading page:', page);

    // Get current filters
    const search = document.querySelector('.search-input')?.value || '';
    const status = document.querySelector('.status-select')?.value || '';
    const jurusan = document.querySelector('.jurusan-select')?.value || '';

    // Build URL with parameters
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (jurusan) params.append('jurusan', jurusan);
    // Sertakan sort jika tersedia
    try {
      if (window.adminSearchFilter && window.adminSearchFilter.currentFilters) {
        var cf = window.adminSearchFilter.currentFilters;
        if (cf.sortBy) params.append('sortBy', cf.sortBy);
        if (cf.sortDir) params.append('sortDir', cf.sortDir);
      } else {
        var qsSort = new URLSearchParams(window.location.search || '');
        if (qsSort.get('sortBy')) params.append('sortBy', qsSort.get('sortBy'));
        if (qsSort.get('sortDir')) params.append('sortDir', qsSort.get('sortDir'));
      }
    } catch (_) {}
    params.append('page', page); // Always include page parameter

    // Tambahkan cache-busting param untuk memastikan data terbaru
    params.append('_ts', Date.now());
    const url = '<?= site_url('Admin/MasterData/users') ?>' + (params.toString() ? '?' + params.toString() : '');

    console.log('Loading URL:', url);

    // Show loading state
    const tbody = document.getElementById('users-tbody');
    const paginationContainer = document.querySelector('.pagination-container');

    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center"><div class="text-gray-500">Loading...</div></td></tr>';
    }

    // Make AJAX request
    fetch(url, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        console.log('Pagination response:', data);

        if (data.success) {
          // Update table body
          if (tbody && data.html) {
            tbody.innerHTML = data.html;
          }

          // Update pagination
          if (paginationContainer && data.pagination) {
            paginationContainer.innerHTML = data.pagination;
          }

          // Update statistics if provided
          if (data.stats) {
            updateStatistics(data.stats);
          }

          // Update URL without page reload
          const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
          history.pushState({}, '', newUrl);

          console.log('Page loaded successfully');
        } else {
          throw new Error(data.message || 'Failed to load page');
        }
      })
      .catch(error => {
        console.error('Pagination error:', error);

        // Show error message
        if (typeof AdminSearchUtils !== 'undefined' && AdminSearchUtils.showNotification) {
          AdminSearchUtils.showNotification('Gagal memuat halaman: ' + error.message, 'error');
        } else {
          alert('Gagal memuat halaman: ' + error.message);
        }

        // Restore previous content or reload
        location.reload();
      });
  }

  // Edit User Modal init
  window.initializeEditUserModal = function() {
    try {
      var modal = document.getElementById('editUserModal');
      var form = document.getElementById('editUserForm');
      var closeBtn = document.getElementById('closeEditUserModal');
      var cancelBtn = document.getElementById('cancelEditUser');
      var submitBtn = document.getElementById('submitEditUser');

      if (!modal || !form || !submitBtn) return;

      function openEditModal(data) {
        document.getElementById('edit_user_id').value = data.id;
        document.getElementById('edit_nama_lengkap').value = data.namalengkap || '';
        document.getElementById('edit_npm').value = data.npm || '';
        document.getElementById('edit_jurusan').value = data.jurusan || '';
        document.getElementById('edit_status').value = data.status || 'pending';
        document.getElementById('edit_password').value = '';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }

      function closeEditModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        form.reset();
      }

      // Delegasi click untuk tombol edit
      document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-action="edit-user"]');
        if (!btn) return;
        e.preventDefault();
        var data = {
          id: btn.getAttribute('data-user-id'),
          namalengkap: btn.getAttribute('data-namalengkap'),
          npm: btn.getAttribute('data-npm'),
          jurusan: btn.getAttribute('data-jurusan'),
          status: btn.getAttribute('data-status')
        };
        openEditModal(data);
      });

      closeBtn.addEventListener('click', closeEditModal);
      cancelBtn.addEventListener('click', closeEditModal);

      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        submitBtn.disabled = true;
        var st = submitBtn.querySelector('.submit-text');
        var lt = submitBtn.querySelector('.loading-text');
        if (st) st.classList.add('hidden');
        if (lt) lt.classList.remove('hidden');

        var fd = new FormData(form);
        var params = new URLSearchParams();
        fd.forEach(function(v, k) {
          params.append(k, v);
        });
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        params.append('csrf_test_name', csrf);

        (window.api && window.api.post ? window.api.post('<?= site_url('Admin/MasterData/updateUser') ?>', params) :
          fetch('<?= site_url('Admin/MasterData/updateUser') ?>', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': csrf
            },
            body: params.toString()
          }).then(function(r) {
            return r.json();
          }))
        .then(function(res) {
            var data = res && res.data ? res.data : res;
            if (data && data.csrf) {
              var m = document.querySelector('meta[name="csrf-token"]');
              if (m) m.setAttribute('content', data.csrf);
            }
            return data;
          })
          .then(function(data) {
            if (data.success) {
              if (typeof window.showAlert === 'function') window.showAlert('success', 'User berhasil diperbarui', 2500);
              closeEditModal();
              if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
                window.adminSearchFilter.performSearch();
              } else if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/users') === 0) {
                window.location.href = window.location.href;
              } else if (window.__adminSpaNavigate) {
                window.__adminSpaNavigate(window.location.href, false);
              } else {
                location.reload();
              }
            } else {
              if (typeof window.showAlert === 'function') window.showAlert('error', data.message || 'Gagal memperbarui user', 3500);
            }
          })
          .catch(function(err) {
            console.error(err);
            if (typeof window.showAlert === 'function') window.showAlert('error', 'Terjadi kesalahan jaringan', 3500);
          })
          .finally(function() {
            submitBtn.disabled = false;
            if (st) st.classList.remove('hidden');
            if (lt) lt.classList.add('hidden');
          });
      });
    } catch (e) {
      console.error('init edit modal error', e);
    }
  };

  function updateStatistics(stats) {
    // Update statistics cards if they exist
    const totalElement = document.querySelector('.bg-white.rounded-lg.shadow-md.p-4 .text-2xl.font-bold.text-gray-900');
    if (totalElement && stats.total !== undefined) {
      totalElement.textContent = stats.total;
    }

    // You can add more statistics updates here if needed
    console.log('Statistics updated:', stats);
  }

  // Initialize pagination on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initializePagination);
    document.addEventListener('DOMContentLoaded', window.initializeEditUserModal);
  } else {
    window.initializePagination();
    window.initializeEditUserModal();
  }
</script>

<?= $this->endSection() ?>