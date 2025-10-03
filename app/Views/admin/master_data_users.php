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
    if (confirm('Are you sure you want to change this user status?')) {
      fetch('<?= site_url('Admin/MasterData/updateUserStatus') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: `user_id=${userId}&status=${newStatus}&csrf_test_name=${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Use SPA-compatible refresh instead of location.reload()
            if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
              window.adminSearchFilter.performSearch();
            } else {
              // Fallback to SPA navigation refresh
              if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/users') === 0) {
                window.location.href = window.location.href;
              } else if (window.__adminSpaNavigate) {
                window.__adminSpaNavigate(window.location.href, false);
              } else {
                location.reload();
              }
            }

            // Show success notification
            if (typeof AdminSearchUtils !== 'undefined' && AdminSearchUtils.showNotification) {
              AdminSearchUtils.showNotification('Status berhasil diperbarui!', 'success');
            }
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred');
        });
    }
  }

  function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
      fetch('<?= site_url('Admin/MasterData/deleteUser') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: `user_id=${userId}&csrf_test_name=${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Use SPA-compatible refresh instead of location.reload()
            if (typeof window.adminSearchFilter !== 'undefined' && window.adminSearchFilter.performSearch) {
              window.adminSearchFilter.performSearch();
            } else {
              // Fallback to SPA navigation refresh
              if (window.location && window.location.pathname && window.location.pathname.indexOf('/Admin/MasterData/users') === 0) {
                window.location.href = window.location.href;
              } else if (window.__adminSpaNavigate) {
                window.__adminSpaNavigate(window.location.href, false);
              } else {
                location.reload();
              }
            }

            // Show success notification
            if (typeof AdminSearchUtils !== 'undefined' && AdminSearchUtils.showNotification) {
              AdminSearchUtils.showNotification('User berhasil dihapus!', 'success');
            }
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred');
        });
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
    params.append('page', page); // Always include page parameter

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
  } else {
    window.initializePagination();
  }
</script>

<?= $this->endSection() ?>